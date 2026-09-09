#!/usr/bin/env bash
#
# Rebuilds hyphenation dictionaries from the TeX hyphenation patterns of the
# hyph-utf8 project.
#
# Dictionary::parseFile() has no notion of the NEXTLEVEL directive, so a
# two-level LibreOffice dictionary ends up with its compound word list and its
# patterns merged into a single pattern space, which produces wrong
# hyphenation. This script writes single level dictionaries instead: the
# patterns come from hyph-utf8, and where the mapping table names a LibreOffice
# dictionary as a second source, only that file's compound word list
# (everything before NEXTLEVEL) is kept and placed in front of the patterns.
# The word list is worth keeping because the patterns alone do not mark
# compound boundaries.
#
# Locales without an entry in the mapping table are left alone; they keep the
# files that tools/updateHyphenationFilesFromLibreOffice.sh installs.
#
# Both sources are pinned to a commit so that a repeated run produces byte
# identical files. Nothing in the generated files records when the script ran.
#
# Usage:
#     tools/updateHyphenationFilesFromTexHyphen.sh
#
# The pins can be overridden through the environment. Their _DATE companions
# are used for attribution only and have to be kept in sync by hand:
#
#     TEX_HYPHEN_REF=<sha> TEX_HYPHEN_REF_DATE=<yyyy-mm-dd> \
#         tools/updateHyphenationFilesFromTexHyphen.sh
#
# Current commits of the sources:
#
#     git ls-remote git@github.com:hyphenation/tex-hyphen.git HEAD
#     git ls-remote git@github.com:LibreOffice/dictionaries.git HEAD
#
set -euo pipefail

# Keep character classes and sort order reproducible across machines.
export LC_ALL=C

TEX_HYPHEN_REF="${TEX_HYPHEN_REF:-5684c0f51c0b81133db2efbe60a408b4155a3ff5}"
TEX_HYPHEN_REF_DATE="${TEX_HYPHEN_REF_DATE:-2026-02-24}"
LIBREOFFICE_REF="${LIBREOFFICE_REF:-32b006a2c22a4ac7e8ed3f03346f7b3d85a970a4}"
LIBREOFFICE_REF_DATE="${LIBREOFFICE_REF_DATE:-2026-08-22}"

TEX_HYPHEN_REPO='hyphenation/tex-hyphen'
TEX_HYPHEN_PATH='hyph-utf8/tex/generic/hyph-utf8/patterns'
TEX_HYPHEN_RAW="https://raw.githubusercontent.com/$TEX_HYPHEN_REPO/$TEX_HYPHEN_REF/$TEX_HYPHEN_PATH"
LIBREOFFICE_REPO='LibreOffice/dictionaries'
LIBREOFFICE_RAW="https://raw.githubusercontent.com/$LIBREOFFICE_REPO/$LIBREOFFICE_REF"

# The compound word lists carry the licence stated in the header of the
# LibreOffice source dictionaries: the OpenOffice.org adaption is LGPL 2 or
# later, the TeX tables it was derived from are LPPL. The full statement is
# copied verbatim into every generated README_hyph_<locale>.txt.
COMPOUND_LICENCE='LGPL 2 or later (adaption), LPPL (underlying TeX tables)'

# Mapping table, one line per locale:
#
#     <locale>  <hyph-utf8 code>  <LibreOffice compound word list, or "-">
#
# Add a line to migrate a locale. The third column is a path inside the
# LibreOffice dictionaries repository; use "-" for locales that should get the
# hyph-utf8 patterns without a compound word list.
read_mapping() {
cat <<'MAPPING'
de_DE de-1996 de/hyph_de_DE.dic
de_AT de-1996 de/hyph_de_DE.dic
de_CH de-1996 de/hyph_de_DE.dic
MAPPING
}

cd "$(dirname "$0")/.."

DICT_DIR='src/share/files/dictionaries'
WORK='tmp'

# tmp/ is gitignored and kept after the run: tools/checkCompoundArtifacts reads
# tmp/patterns-<code>.txt and tmp/compound-<language>.txt from here.
rm -rf "$WORK"
mkdir -p "$WORK"

abort() {
    echo "${0##*/}: $1" >&2
    exit 1
}

# Download to a scratch name first so that an interrupted transfer can never be
# mistaken for a complete source file.
fetch() {
    curl --silent --show-error --fail --location --output "$2.part" "$1"
    mv "$2.part" "$2"
}

# Sources come from two projects with different conventions: drop a UTF-8 BOM,
# CR line endings and trailing blanks.
normalize() {
    sed -e '1s/^\xEF\xBB\xBF//' -e 's/[[:space:]]*$//' "$1"
}

# Both parseFile() and parse_ini_file() are picky. Lines that either project
# uses for structure rather than for patterns are dropped here: blank lines,
# comments, the upper case directives (COMPOUND*, NOHYPHEN) and the two
# non-standard patterns 1-1 and 1'1.
drop_non_patterns() {
    grep -v '^$' | grep -v '^[#%]' | grep -v '^[A-Z]' | grep -v "^1[-']1\$"
}

count_lines() {
    awk 'END { print NR }' "$1"
}

# Reads the metadata of a hyph-utf8 code back into version, licence_name and
# licence_url.
read_metadata() {
    version="$(cat "$WORK/version-$1.txt")"
    licence_name="$(cat "$WORK/licence-name-$1.txt")"
    licence_url="$(cat "$WORK/licence-url-$1.txt")"
}

# parseFile() skips lines containing "=", and Pattern::setPattern() rewrites a
# straight apostrophe to U+2019, which no input word will ever match. Neither
# case occurs in the sources today; report them so that a future source change
# does not slip through silently.
report_unusable() {
    local file="$1" label="$2" equals apostrophes
    equals="$(grep -c '=' "$file" || true)"
    apostrophes="$(grep -c "'" "$file" || true)"
    echo "  $label: $(count_lines "$file") lines, $equals with \"=\", $apostrophes with \"'\""
    if [ "$equals" -ne 0 ]; then
        echo "  warning: Dictionary::parseFile() drops the $equals line(s) containing \"=\"" >&2
    fi
    if [ "$apostrophes" -ne 0 ]; then
        echo "  warning: check the rendered .ini for the $apostrophes line(s) containing \"'\"," >&2
        echo "  warning: Pattern::setPattern() turns a straight apostrophe into U+2019" >&2
    fi
}

# --- hyphenation patterns ---------------------------------------------------

while read -r code; do
    echo "hyph-utf8 $code"
    fetch "$TEX_HYPHEN_RAW/txt/hyph-$code.pat.txt" "$WORK/hyph-$code.pat.txt"
    fetch "$TEX_HYPHEN_RAW/tex/hyph-$code.tex" "$WORK/hyph-$code.tex"

    normalize "$WORK/hyph-$code.pat.txt" | drop_non_patterns > "$WORK/patterns-$code.txt"
    report_unusable "$WORK/patterns-$code.txt" 'patterns'

    # The .tex file opens with a YAML-ish comment header, terminated by a rule
    # of "=" characters. It is the only place that states version and licence.
    awk '/^% =+$/ { exit } /^%/ { print; next } { exit }' \
        "$WORK/hyph-$code.tex" > "$WORK/header-$code.txt"

    awk '/^% licence:/ { block = 1; print; next }
         block && /^% [a-z_]+:/ { exit }
         block { print }' "$WORK/header-$code.txt" > "$WORK/licence-$code.txt"
    if ! grep -q '^%[[:space:]]*text:' "$WORK/licence-$code.txt"; then
        abort "hyph-$code.tex states no licence text - refusing to redistribute the patterns"
    fi

    # Keep the three extracted values in tmp/ rather than in an associative
    # array, so that the script also runs on the bash 3.2 that ships with
    # macOS. read_metadata() below reads them back.
    version="$(awk '/^% version:/ {
        sub(/^% version:[[:space:]]*/, ""); print; exit }' "$WORK/header-$code.txt")"
    if [ -z "$version" ]; then
        # hyph-en-gb.tex, for one, carries no version. Fall back to the pin.
        version="git-${TEX_HYPHEN_REF:0:12} ($TEX_HYPHEN_REF_DATE)"
    fi
    printf '%s\n' "$version" > "$WORK/version-$code.txt"

    licence_name="$(awk '/^%[[:space:]]+name:/ {
        sub(/^%[[:space:]]+name:[[:space:]]*/, ""); print; exit }' "$WORK/licence-$code.txt")"
    if [ -z "$licence_name" ]; then
        licence_name='custom permissive licence (see text)'
    fi
    printf '%s\n' "$licence_name" > "$WORK/licence-name-$code.txt"

    awk '/^%[[:space:]]+url:/ {
        sub(/^%[[:space:]]+url:[[:space:]]*/, ""); print; exit }' \
        "$WORK/licence-$code.txt" > "$WORK/licence-url-$code.txt"

    echo "  version: $version, licence: $licence_name"
done < <(read_mapping | awk '{ print $2 }' | sort -u)

# --- compound word lists ----------------------------------------------------

while read -r compound_path; do
    language="${compound_path%%/*}"
    echo "LibreOffice $compound_path"
    fetch "$LIBREOFFICE_RAW/$compound_path" "$WORK/libreoffice-$language.dic"

    # Line 1 names the character set, in the spelling parseFile() expects.
    charset="$(head -1 "$WORK/libreoffice-$language.dic" | tr -d '\r' | sed 's/^ISO8859/ISO-8859/')"
    iconv -f "$charset" -t UTF-8 "$WORK/libreoffice-$language.dic" \
        > "$WORK/libreoffice-$language.utf8"

    if ! grep -q '^NEXTLEVEL$' "$WORK/libreoffice-$language.utf8"; then
        abort "$compound_path has no NEXTLEVEL - the source layout changed, refusing to guess"
    fi

    # Everything between the header and NEXTLEVEL is the compound word list.
    normalize "$WORK/libreoffice-$language.utf8" \
        | awk 'NR == 1 { next } /^NEXTLEVEL$/ { stop = 1 } ! stop { print }' \
        | drop_non_patterns > "$WORK/compound-$language.txt"
    report_unusable "$WORK/compound-$language.txt" 'compound word list'

    # Leading comment block, for verbatim attribution in the README.
    awk 'NR == 1 { next } /^#/ { print; next } { exit }' "$WORK/libreoffice-$language.utf8" \
        | grep -v '^#\{10,\}$' \
        | sed -e 's/^#//' -e 's/^ //' -e 's/[[:space:]]*#$//' -e 's/[[:space:]]*$//' \
        > "$WORK/compound-header-$language.txt"
done < <(read_mapping | awk '$3 != "-" { print $3 }' | sort -u)

# --- dictionaries and per locale READMEs ------------------------------------

while read -r locale code compound_path; do
    echo "building hyph_$locale.dic"
    read_metadata "$code"
    dic="$WORK/hyph_$locale.dic"
    readme="$WORK/README_hyph_$locale.txt"

    cat > "$dic" <<EOF
UTF-8
#
# Hyphenation dictionary for $locale.
#
# Generated by tools/updateHyphenationFilesFromTexHyphen.sh - do not edit.
# See README_hyph_$locale.txt for the full licences and SOURCES.md for the
# state of every locale in this directory.
#
# The file is single level on purpose: Dictionary::parseFile() does not know
# the NEXTLEVEL directive, so all patterns share one pattern space.
#
# Hyphenation patterns
#   source:  $TEX_HYPHEN_REPO, $TEX_HYPHEN_PATH/txt/hyph-$code.pat.txt
#   code:    $code
#   version: $version
#   commit:  $TEX_HYPHEN_REF ($TEX_HYPHEN_REF_DATE)
#   licence: $licence_name${licence_url:+, $licence_url}
EOF

    cat > "$readme" <<EOF
Hyphenation dictionary hyph_$locale.dic

Generated by tools/updateHyphenationFilesFromTexHyphen.sh - do not edit.

EOF

    if [ "$compound_path" != '-' ]; then
        language="${compound_path%%/*}"
        cat >> "$dic" <<EOF
#
# Compound word list (in front of the patterns; where both define the same
# text, parse_ini_file() keeps the last one, so the patterns win)
#   source:  $LIBREOFFICE_REPO, $compound_path
#   commit:  $LIBREOFFICE_REF ($LIBREOFFICE_REF_DATE)
#   licence: $COMPOUND_LICENCE
#
EOF
        cat >> "$readme" <<EOF
This dictionary combines two sources. Section 1 describes the hyphenation
patterns, section 2 the compound word list that precedes them.


1. Hyphenation patterns
=======================

Repository: https://github.com/$TEX_HYPHEN_REPO
File:       $TEX_HYPHEN_PATH/txt/hyph-$code.pat.txt
Commit:     $TEX_HYPHEN_REF ($TEX_HYPHEN_REF_DATE)

The header of the matching pattern definition follows verbatim, from
$TEX_HYPHEN_PATH/tex/hyph-$code.tex:

EOF
        sed -e 's/^%//' -e 's/^ //' "$WORK/header-$code.txt" >> "$readme"
        cat >> "$readme" <<EOF


2. Compound word list
=====================

Repository: https://github.com/$LIBREOFFICE_REPO
File:       $compound_path
Commit:     $LIBREOFFICE_REF ($LIBREOFFICE_REF_DATE)
Licence:    $COMPOUND_LICENCE

Only the compound word list of that file is used, that is everything before
its NEXTLEVEL directive. Its own pattern level is replaced by the patterns of
section 1. The header of the file follows verbatim.

EOF
        sed '/./s/^/    /' "$WORK/compound-header-$language.txt" >> "$readme"
        cat "$WORK/compound-$language.txt" >> "$dic"
    else
        cat >> "$dic" <<EOF
#
EOF
        cat >> "$readme" <<EOF
Hyphenation patterns
====================

Repository: https://github.com/$TEX_HYPHEN_REPO
File:       $TEX_HYPHEN_PATH/txt/hyph-$code.pat.txt
Commit:     $TEX_HYPHEN_REF ($TEX_HYPHEN_REF_DATE)

The header of the matching pattern definition follows verbatim, from
$TEX_HYPHEN_PATH/tex/hyph-$code.tex:

EOF
        sed -e 's/^%//' -e 's/^ //' "$WORK/header-$code.txt" >> "$readme"
    fi

    cat "$WORK/patterns-$code.txt" >> "$dic"
done < <(read_mapping)

# --- install ----------------------------------------------------------------

# Nothing has touched the dictionary directory so far: a download, a missing
# header or a changed source layout aborts above and leaves it untouched.
while read -r locale code compound_path; do
    mv "$WORK/hyph_$locale.dic" "$DICT_DIR/hyph_$locale.dic"
    mv "$WORK/README_hyph_$locale.txt" "$DICT_DIR/README_hyph_$locale.txt"
done < <(read_mapping)

# --- manifest ---------------------------------------------------------------

migrated_count=0
remaining_count=0
remaining_with_ini=0
mapped="$(read_mapping | awk '{ print $1 }' | sort -u)"
for locale in $mapped; do
    migrated_count=$((migrated_count + 1))
done
remaining="$(comm -23 \
    <(cd "$DICT_DIR" && ls hyph_*.dic | sed -e 's/^hyph_//' -e 's/\.dic$//' | sort) \
    <(printf '%s\n' "$mapped"))"
for locale in $remaining; do
    remaining_count=$((remaining_count + 1))
    if [ -f "$DICT_DIR/$locale.ini" ]; then
        remaining_with_ini=$((remaining_with_ini + 1))
    fi
done
dic_count=$((migrated_count + remaining_count))

echo "writing $DICT_DIR/SOURCES.md"
{
    cat <<EOF
# Dictionary sources

Generated by \`tools/updateHyphenationFilesFromTexHyphen.sh\` - do not edit.

The runtime reads \`<locale>.ini\`; \`tools/renderDicts\` renders those from the
\`hyph_<locale>.dic\` files, and both are committed.

## Locales built from hyph-utf8 patterns

| Locale | Sources | hyph-utf8 code | Version | Licence | Commits |
| ------ | ------- | -------------- | ------- | ------- | ------- |
EOF
    while read -r locale code compound_path; do
        read_metadata "$code"
        sources='hyph-utf8 patterns'
        licence="$licence_name (patterns)"
        commits="tex-hyphen ${TEX_HYPHEN_REF:0:12}"
        if [ "$compound_path" != '-' ]; then
            sources="$sources + LibreOffice compound word list (\`$compound_path\`)"
            licence="$licence, $COMPOUND_LICENCE"
            commits="$commits, LibreOffice ${LIBREOFFICE_REF:0:12}"
        fi
        echo "| \`$locale\` | $sources | \`$code\` | $version | $licence | $commits |"
    done < <(read_mapping)

    cat <<EOF

Pinned commits in full:

- $TEX_HYPHEN_REPO \`$TEX_HYPHEN_REF\` ($TEX_HYPHEN_REF_DATE)
- $LIBREOFFICE_REPO \`$LIBREOFFICE_REF\` ($LIBREOFFICE_REF_DATE)

The compound word list is written in front of the patterns. Where both define
the same text, \`parse_ini_file()\` keeps the last one, so a pattern wins over a
word list entry. \`NEXTLEVEL\`, \`NOHYPHEN\`, \`1-1\` and \`1'1\` are not carried
over, because \`Dictionary::parseFile()\` supports neither hyphenation levels
nor non-standard hyphenation.

Every migrated locale has a \`README_hyph_<locale>.txt\` next to its dictionary
that carries the verbatim upstream headers, including the full licence texts.

## Locales still sourced from LibreOffice

Of the $dic_count \`hyph_*.dic\` files in this directory, $migrated_count are built by the script
above. The other $remaining_count are the files that
\`tools/updateHyphenationFilesFromLibreOffice.sh\` installs, and this script
leaves them untouched. The LibreOffice script copies every locale, including
the ones listed above, and therefore runs this script again at its end so
that they keep their hyph-utf8 dictionaries. $remaining_with_ini of the
$remaining_count have a rendered \`.ini\`:

| Locale | Rendered \`.ini\` |
| ------ | -------------- |
EOF
    for locale in $remaining; do
        if [ -f "$DICT_DIR/$locale.ini" ]; then
            echo "| \`$locale\` | yes |"
        else
            echo "| \`$locale\` | no |"
        fi
    done

    cat <<EOF

A locale without a rendered \`.ini\` is invisible to the runtime.
\`hyph_sr-Latn.dic\` is the known case: the file name pattern in \`tools/renderDicts\`
(\`hyph_([\\w\\_]+)\\.dic\`) does not allow a hyphen.

Scripts without upper and lower case are a second known limit. \`parseFile()\`
skips every line that equals its own upper case form, so their dictionaries
render empty; \`te_IN.ini\` is 0 bytes for that reason.

## How to update

Run the script from the repository root:

\`\`\`bash
tools/updateHyphenationFilesFromTexHyphen.sh
\`\`\`

It rewrites the dictionaries of the locales in its mapping table, renders the
\`.ini\` files and regenerates this file. A second run with the same pins
produces byte identical output.

To move to newer sources, look up the current commits and pass them in
together with their dates:

\`\`\`bash
git ls-remote git@github.com:$TEX_HYPHEN_REPO.git HEAD
git ls-remote git@github.com:$LIBREOFFICE_REPO.git HEAD

TEX_HYPHEN_REF=<sha> TEX_HYPHEN_REF_DATE=<yyyy-mm-dd> \\
LIBREOFFICE_REF=<sha> LIBREOFFICE_REF_DATE=<yyyy-mm-dd> \\
    tools/updateHyphenationFilesFromTexHyphen.sh
\`\`\`

Then update the defaults at the top of the script.

To migrate another locale, add a line to the mapping table in the script: the
library locale, the hyph-utf8 language code and either the path of a
LibreOffice dictionary whose compound word list should be kept, or \`-\`. The
language code is the \`tag\` field in
\`$TEX_HYPHEN_PATH/tex/hyph-<code>.tex\`.
EOF
} > "$DICT_DIR/SOURCES.md"

# --- render and verify ------------------------------------------------------

echo 'rendering .ini files'
tools/renderDicts

# renderDicts swallows exceptions from parseFile(), so a failed conversion
# leaves an empty or truncated .ini behind instead of an error. Every source
# line has to come out as exactly one .ini line. The only lines parseFile()
# is known to drop are those containing "=" (reported above), so they are
# taken out of the expected count; anything else missing is an error.
usable_lines() {
    echo $(( $(count_lines "$1") - $(grep -c '=' "$1" || true) ))
}

while read -r locale code compound_path; do
    ini="$DICT_DIR/$locale.ini"
    expected="$(usable_lines "$WORK/patterns-$code.txt")"
    if [ "$compound_path" != '-' ]; then
        expected=$((expected + $(usable_lines "$WORK/compound-${compound_path%%/*}.txt")))
    fi
    if [ ! -f "$ini" ]; then
        abort "$ini was not rendered"
    fi
    rendered="$(count_lines "$ini")"
    if [ "$rendered" -ne "$expected" ]; then
        abort "$ini has $rendered lines but $expected were expected - Dictionary::parseFile() dropped or failed on some lines"
    fi
    echo "  $locale.ini: $rendered lines from $expected source lines"
done < <(read_mapping)

echo 'done'
