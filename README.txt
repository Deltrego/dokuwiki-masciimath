MASCIIMathML
============

Syntax plugin for Dokuwiki that parses ASCIIMathML code into MathML output.

This is an implementation of Peter Jipsen's ASCIIMathML.js, but in pure PHP, i.e. the translation is done serverside, and no JavaScript is necessary. Only the symbolic math part (the original part) is included, not the SVG module.

To try it out:
  * Install the plugin, i.e. create a folder called "masciimath" in the Dokuwiki plugin folder, and put the plugin files here.
  * Create a wikipage with the following ASCIIMathML code in it: `x_(1,2)=(-b+-sqrt(b^2-4a c))/(2a)`
  * View your wiki using Firefox (the only browser with a complete MathML implementation).

Known bugs/variations from the reference implementation (Jipsen's):
  * Does not recognize double dollars form, only backticks, i.e. does not recognize $$a=b+c$$, only `a=b+c`
  * Does not recognize the form `text{mintekst}` for mtext-tags, only `"mintekst"`
  * Does not recognize LaTeX-style syntax, i.e. \Alpha for Alpha
  * Uses only (: and :) for angular brackets. Jipsen's documentation is ambigious, sometimes using (: and :), sometimes << and >>
  * The latter are used for "much greater than" and "much less than" here.
  * Recognizes the forms `int_0^1` and `int^1_0` as equivalent. Jipsen's only recognizes the former. This applies no matter the base.
  * Recognizes all standard Greek letters (i.e. Alpha and gamma), uppercase and lowercase, whereas Jipsen's only recognizes a subset
  * Defaults to displaystyle=false (inline style). However, displaystyle=true can be trigged by including a single whitespace as the first character, such as: ` int_0^x x`
  * One pair of parantheses is removed when the expression can be parsed uniquely without it as usual, but here this only applies to ( and ), not the other kind of brackets.

Dokuwiki plugin homepage: https://www.dokuwiki.org/plugin:masciimath
GitHub repository: https://github.com/M0rtenB/dokuwiki-masciimath

Jipsen's original implementation ASCIIMathML.js: http://www1.chapman.edu/~jipsen/mathml/asciimath.html

Morten B. Nielsen, mortenb@gmail.com