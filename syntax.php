<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Morten Nielsen <mortenb@gmail.com>
 * 
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_masciimath extends DokuWiki_Syntax_Plugin {

    public $syms;
    public $parsestr;

    function getType(){ return 'substition'; }
    function getSort(){ return 300; }
    function connectTo($mode) { $this->Lexer->addSpecialPattern('`.*?`',$mode,'plugin_masciimath'); }

    function setup_syms() {
        $this->syms = array(

        # Array of associative arrays, der rummer disse keys:
        #   'ascii' er token i rå ascii-form
        #   'ml' er token på string-form, med evt yderste parantes bevaret
        #   'ml_nobr' er token på string-form med evt yderste parantes skrællet af (medmindre det er pipes)
        #   'ml_sign' og 'ml_op' er kode for symboler i sekundær rolle, som sign (unært minus) og som svag operator (pipe)
        #   'row' er token som array af komma-separerede elementer (kun til brug for matrix-logikken)
        #   'type' bestemmer en operators type:
        #      'op' er minimal præ- eller infix-operator der står som symbol i en row; parseren opfatter et påfølgende minus som unær operator
        #      'leftbracket' og 'rightbracket' er parantestegn, dog ikke '|' som får særbehandling
        #      'infix' er operatorer som ^, _, /, og //
        #      'unary' er operatorer som sqrt, hat og fr
        #      'binary' er operatorer som 'root' og 'stackrel'
        #      'func' er operatorer som sin, cos og lim (der binder stærkt)
        #      'magnum' er operatorer som int, sum og prod (der binder svagt)
        #   'underover' er sand for operatorer som prod og lim, der foretrækker at have sub/super-script stående "under/over" sig
        #   'wrap_left' og 'wrap_right' forklarer hvordan en operator implementeres

        // Signs ('sign')
        array ('ascii' => '+', 'ml' => '<mo>&plus;</mo>', 'type' => 'sign', 'ml_sign' => '<mo>&plus;</mo>'),
        array ('ascii' => '+ ', 'ml' => '<mo>&plus;</mo>'),
        array ('ascii' => '-', 'ml' => '<mo>&minus;</mo>', 'type' => 'sign', 'ml_sign' => '<mo lspace="1px" rspace="1px">&#x2010;</mo>'),   # &#x2010; is HYPHEN
        array ('ascii' => '- ', 'ml' => '<mo>&minus;</mo>'),
        array ('ascii' => '+-', 'ml' => '<mo>&plusmn;</mo>', 'type' => 'sign', 'ml_sign' => '<mo>&plusmn;</mo>'),
        array ('ascii' => '+- ', 'ml' => '<mo>&plusmn;</mo>'),

        // Symbolic operators ('op')
        array ('ascii' => ',', 'ml' => '<mo>,</mo>', 'type' => 'op'),
        array ('ascii' => ':', 'ml' => '<mo>:</mo>', 'type' => 'op'),
        array ('ascii' => ';', 'ml' => '<mo>;</mo>', 'type' => 'op'),
        array ('ascii' => '=', 'ml' => '<mo>=</mo>', 'type' => 'op'),
        array ('ascii' => '\\\\', 'ml' => '<mo>\</mo>', 'type' => 'op'), # backslash
        array ('ascii' => '\ ', 'ml' => '<mo>&nbsp;</mo>', 'type' => 'op'),
        array ('ascii' => '*', 'ml' => '<mo>&sdot;</mo>', 'type' => 'op'),
        array ('ascii' => '**', 'ml' => '<mo>&#x22C6;</mo>', 'type' => 'op'),
        array ('ascii' => 'xx', 'ml' => '<mo>&times;</mo>', 'type' => 'op'),
        array ('ascii' => 'times', 'ml' => '<mo>&times;</mo>', 'type' => 'op'),
        array ('ascii' => '-:', 'ml' => '<mo>&divide;</mo>', 'type' => 'op'),
        array ('ascii' => '@', 'ml' => '<mo>&#x2218;</mo>', 'type' => 'op'),
        array ('ascii' => 'o+', 'ml' => '<mo>&oplus;</mo>', 'type' => 'op'),
        array ('ascii' => 'ox', 'ml' => '<mo>&otimes;</mo>', 'type' => 'op'),
        array ('ascii' => 'o.', 'ml' => '<mo>&#x2299;</mo>', 'type' => 'op'),
        array ('ascii' => '^^', 'ml' => '<mo>&and;</mo>', 'type' => 'op'),
        array ('ascii' => 'vv', 'ml' => '<mo>&or;</mo>', 'type' => 'op'),
        array ('ascii' => 'nn', 'ml' => '<mo>&cap;</mo>', 'type' => 'op'),
        array ('ascii' => 'uu', 'ml' => '<mo>&cup;</mo>', 'type' => 'op'),
        array ('ascii' => '!=', 'ml' => '<mo>&ne;</mo>', 'type' => 'op'),
        array ('ascii' => 'ne', 'ml' => '<mo>&ne;</mo>', 'type' => 'op'),
        array ('ascii' => '<', 'ml' => '<mo>&lt;</mo>', 'type' => 'op'),
        array ('ascii' => 'lt', 'ml' => '<mo>&lt;</mo>', 'type' => 'op'),
        array ('ascii' => '>', 'ml' => '<mo>&gt;</mo>', 'type' => 'op'),
        array ('ascii' => 'gt', 'ml' => '<mo>&gt;</mo>', 'type' => 'op'),
        array ('ascii' => '<=', 'ml' => '<mo>&le;</mo>', 'type' => 'op'),
        array ('ascii' => 'le', 'ml' => '<mo>&le;</mo>', 'type' => 'op'),
        array ('ascii' => '>=', 'ml' => '<mo>&ge;</mo>', 'type' => 'op'),
        array ('ascii' => 'ge', 'ml' => '<mo>&ge;</mo>', 'type' => 'op'),
        array ('ascii' => '<<', 'ml' => '<mo>&#x226a;</mo>', 'type' => 'op'),
        array ('ascii' => '>>', 'ml' => '<mo>&#x226b;</mo>', 'type' => 'op'),
        array ('ascii' => '>-', 'ml' => '<mo>&#x227B;</mo>', 'type' => 'op'),
        array ('ascii' => '-<', 'ml' => '<mo>&#x227A;</mo>', 'type' => 'op'),
        array ('ascii' => 'in', 'ml' => '<mo>&isin;</mo>', 'type' => 'op'),
        array ('ascii' => '!in', 'ml' => '<mo>&notin;</mo>', 'type' => 'op'),
        array ('ascii' => 'sub', 'ml' => '<mo>&sub;</mo>', 'type' => 'op'),
        array ('ascii' => 'sup', 'ml' => '<mo>&sup;</mo>', 'type' => 'op'),
        array ('ascii' => '!sub', 'ml' => '<mo>&nsub;</mo>', 'type' => 'op'),
        array ('ascii' => 'nsub', 'ml' => '<mo>&nsub;</mo>', 'type' => 'op'),
        array ('ascii' => 'sube', 'ml' => '<mo>&sube;</mo>', 'type' => 'op'),
        array ('ascii' => 'supe', 'ml' => '<mo>&supe;</mo>', 'type' => 'op'),
        array ('ascii' => '-=', 'ml' => '<mo>&equiv;</mo>', 'type' => 'op'),
        array ('ascii' => '~=', 'ml' => '<mo>&cong;</mo>', 'type' => 'op'),
        array ('ascii' => '~~', 'ml' => '<mo>&asymp;</mo>', 'type' => 'op'),
        array ('ascii' => 'approx', 'ml' => '<mo>&asymp;</mo>', 'type' => 'op'),
        array ('ascii' => 'prop', 'ml' => '<mo>&prop;</mo>', 'type' => 'op'),
        array ('ascii' => 'not', 'ml' => '<mo>&not;</mo>', 'type' => 'op'),
        array ('ascii' => 'AA', 'ml' => '<mo>&forall;</mo>', 'type' => 'op'),
        array ('ascii' => 'EE', 'ml' => '<mo>&exist;</mo>', 'type' => 'op'),
        array ('ascii' => '|--', 'ml' => '<mo>&#x22A2;</mo>', 'type' => 'op'),
        array ('ascii' => '|==', 'ml' => '<mo>&#x22A8;</mo>', 'type' => 'op'),
        array ('ascii' => '/_', 'ml' => '<mo>&ang;</mo>', 'type' => 'op'),
        array ('ascii' => 'ang', 'ml' => '<mo>&ang;</mo>', 'type' => 'op'),
        array ('ascii' => ':.', 'ml' => '<mo>&there4;</mo>', 'type' => 'op'),
        array ('ascii' => '<-', 'ml' => '<mo>&larr;</mo>', 'type' => 'op'),
        array ('ascii' => 'larr', 'ml' => '<mo>&larr;</mo>', 'type' => 'op'),
        array ('ascii' => 'uarr', 'ml' => '<mo>&uarr;</mo>', 'type' => 'op'),
        array ('ascii' => 'rarr', 'ml' => '<mo>&rarr;</mo>', 'type' => 'op'),
        array ('ascii' => '->', 'ml' => '<mo>&rarr;</mo>', 'type' => 'op'),
        array ('ascii' => 'darr', 'ml' => '<mo>&darr;</mo>', 'type' => 'op'),
        array ('ascii' => '<->', 'ml' => '<mo>&harr;</mo>', 'type' => 'op'),
        array ('ascii' => 'harr', 'ml' => '<mo>&harr;</mo>', 'type' => 'op'),
        array ('ascii' => '|->', 'ml' => '<mo>&#x21A6;</mo>', 'type' => 'op'),
        array ('ascii' => 'rArr', 'ml' => '<mo>&rArr;</mo>', 'type' => 'op'),
        array ('ascii' => '=>', 'ml' => '<mo>&rArr;</mo>', 'type' => 'op'),
        array ('ascii' => 'lArr', 'ml' => '<mo>&lArr;</mo>', 'type' => 'op'),
        array ('ascii' => 'hArr', 'ml' => '<mo>&hArr;</mo>', 'type' => 'op'),
        array ('ascii' => 'iff', 'ml' => '<mo>&hArr;</mo>', 'type' => 'op'),
        array ('ascii' => '<=>', 'ml' => '<mo>&hArr;</mo>', 'type' => 'op'),
        array ('ascii' => '|__', 'ml' => '<mo>&lfloor;</mo>', 'type' => 'op'),
        array ('ascii' => '__|', 'ml' => '<mo>&rfloor;</mo>', 'type' => 'op'),
        array ('ascii' => '|~', 'ml' => '<mo>&lceil;</mo>', 'type' => 'op'),
        array ('ascii' => '~|', 'ml' => '<mo>&rceil;</mo>', 'type' => 'op'),
        array ('ascii' => 'if', 'ml' => '<mi>if</mi>', 'type' => 'op'),
        array ('ascii' => 'and', 'ml' => '<mi>and</mi>', 'type' => 'op'),
        array ('ascii' => 'or', 'ml' => '<mi>or</mi>', 'type' => 'op'),

        // Brackets ('leftbracket' and 'rightbracket')
        array ('ascii' => '(', 'ml' => '<mo>(</mo>', 'type' => 'leftbracket'),
        array ('ascii' => ')', 'ml' => '<mo>)</mo>', 'type' => 'rightbracket'),
        array ('ascii' => '[', 'ml' => '<mo>[</mo>', 'type' => 'leftbracket'),
        array ('ascii' => ']', 'ml' => '<mo>]</mo>', 'type' => 'rightbracket'),
        array ('ascii' => '{', 'ml' => '<mo>{</mo>', 'type' => 'leftbracket'),
        array ('ascii' => '}', 'ml' => '<mo>}</mo>', 'type' => 'rightbracket'),
        array ('ascii' => '(:', 'ml' => '<mo>&lang;</mo>', 'type' => 'leftbracket'),
        array ('ascii' => ':)', 'ml' => '<mo>&rang;</mo>', 'type' => 'rightbracket'),
        array ('ascii' => '{:', 'ml' => '', 'type' => 'leftbracket'),
        array ('ascii' => ':}', 'ml' => '', 'type' => 'rightbracket'),
        array ('ascii' => '|', 'ml' => '<mo>|</mo>', 'ml_op' => '<mo form="infix" fence="false" separator="true">|</mo>'),

        // Special operators
        array ('ascii' => '^', 'ml' => '<mo>^</mo>', 'type' => 'infix', 'wrap_left' => '<msup>', 'wrap_right' => '</msup>'),
        array ('ascii' => '_',  'ml' => '<mo>_</mo>', 'type' => 'infix', 'wrap_left' => '<msub>', 'wrap_right' => '</msub>'),
        array ('ascii' => '/', 'ml' => '<mo>/</mo>', 'type' => 'infix', 'wrap_left' => '<mfrac>', 'wrap_right' => '</mfrac>'),
        array ('ascii' => '//', 'ml' => '<mo>&frasl;</mo>', 'type' => 'infix', 'wrap_left' => '<mfrac bevelled="true">', 'wrap_right' => '</mfrac>'),
        array ('ascii' => 'sqrt', 'type' => 'unary', 'wrap_left' => '<msqrt>', 'wrap_right' => '</msqrt>'),
        array ('ascii' => 'hat', 'type' => 'unary', 'wrap_left' => '<mover accent=false>', 'wrap_right' => '<mo>^</mo></mover>'),
        array ('ascii' => 'bar', 'type' => 'unary', 'wrap_left' => '<mover accent=false>', 'wrap_right' => '<mo>&macr;</mo></mover>'),
        array ('ascii' => 'ul', 'type' => 'unary', 'wrap_left' => '<munder>', 'wrap_right' => '<mo>&UnderBar;</mo></munder>'),
        array ('ascii' => 'vec', 'type' => 'unary', 'wrap_left' => '<mover accent=false>', 'wrap_right' => '<mo>&rarr;</mo></mover>'),
        array ('ascii' => 'dot', 'type' => 'unary', 'wrap_left' => '<mover accent=false>', 'wrap_right' => '<mo>.</mo></mover>'),
        array ('ascii' => 'ddot', 'type' => 'unary', 'wrap_left' => '<mover accent=false>', 'wrap_right' => '<mo>..</mo></mover>'),
        array ('ascii' => 'root', 'type' => 'binary', 'wrap_left' => '<mroot>', 'wrap_right' => '</mroot>'),
        array ('ascii' => 'stackrel', 'type' => 'binary', 'wrap_left' => '<mover>', 'wrap_right' => '</mover>'),
        
        // Mathvariant operators
        array ('ascii' => 'bb', 'type' => 'unary', 'wrap_left' => '<mstyle mathvariant="bold">', 'wrap_right' => '</mstyle>'),
        array ('ascii' => 'bbb', 'type' => 'unary', 'wrap_left' => '<mstyle mathvariant="double-struck">', 'wrap_right' => '</mstyle>'),
        array ('ascii' => 'cc', 'type' => 'unary', 'wrap_left' => '<mstyle mathvariant="script">', 'wrap_right' => '</mstyle>'),
        array ('ascii' => 'fr', 'type' => 'unary', 'wrap_left' => '<mstyle mathvariant="fraktur">', 'wrap_right' => '</mstyle>'),
        array ('ascii' => 'tt', 'type' => 'unary', 'wrap_left' => '<mstyle mathvariant="monospace">', 'wrap_right' => '</mstyle>'),
        array ('ascii' => 'sf', 'type' => 'unary', 'wrap_left' => '<mstyle mathvariant="sans-serif">', 'wrap_right' => '</mstyle>'),

        // Magnum operators
        array ('ascii' => 'sum', 'ml' => '<mo>&sum;</mo>', 'type' => 'magnum', 'underover' => true),
        array ('ascii' => 'prod', 'ml' => '<mo>&prod;</mo>', 'type' => 'magnum', 'underover' => true),
        array ('ascii' => '^^^', 'ml' => '<mo>&#x22C0;</mo>', 'type' => 'magnum', 'underover' => true),
        array ('ascii' => 'vvv', 'ml' => '<mo>&#x22C1;</mo>', 'type' => 'magnum', 'underover' => true),
        array ('ascii' => 'nnn', 'ml' => '<mo>&#x22C2;</mo>', 'type' => 'magnum', 'underover' => true),
        array ('ascii' => 'uuu', 'ml' => '<mo>&#x22C3;</mo>', 'type' => 'magnum', 'underover' => true),
        array ('ascii' => 'int', 'ml' => '<mo>&int;</mo>', 'type' => 'magnum'),
        array ('ascii' => 'oint', 'ml' => '<mo>&#x222E;</mo>', 'type' => 'magnum'),
        array ('ascii' => 'ointoint', 'ml' => '<mo>&#x222F;</mo>', 'type' => 'magnum'),   # MortenB's idea

        // Standard functions
        array ('ascii' => 'sin', 'ml' => '<mi>sin</mi>', 'type' => 'func'),
        array ('ascii' => 'cos', 'ml' => '<mi>cos</mi>', 'type' => 'func'),
        array ('ascii' => 'tan', 'ml' => '<mi>tan</mi>', 'type' => 'func'),
        array ('ascii' => 'arcsin', 'ml' => '<mi>arcsin</mi>', 'type' => 'func'),
        array ('ascii' => 'arccos', 'ml' => '<mi>arccos</mi>', 'type' => 'func'),
        array ('ascii' => 'arctan', 'ml' => '<mi>arctan</mi>', 'type' => 'func'),
        array ('ascii' => 'sinh', 'ml' => '<mi>sinh</mi>', 'type' => 'func'),
        array ('ascii' => 'cosh', 'ml' => '<mi>cosh</mi>', 'type' => 'func'),
        array ('ascii' => 'tanh', 'ml' => '<mi>tanh</mi>', 'type' => 'func'),
        array ('ascii' => 'cot', 'ml' => '<mi>cot</mi>', 'type' => 'func'),
        array ('ascii' => 'sec', 'ml' => '<mi>sec</mi>', 'type' => 'func'),
        array ('ascii' => 'csc', 'ml' => '<mi>csc</mi>', 'type' => 'func'),
        array ('ascii' => 'coth', 'ml' => '<mi>coth</mi>', 'type' => 'func'),
        array ('ascii' => 'sech', 'ml' => '<mi>sech</mi>', 'type' => 'func'),
        array ('ascii' => 'csch', 'ml' => '<mi>csch</mi>', 'type' => 'func'),
        array ('ascii' => 'exp', 'ml' => '<mi>exp</mi>', 'type' => 'func'),
        array ('ascii' => 'log', 'ml' => '<mi>log</mi>', 'type' => 'func'),
        array ('ascii' => 'ln', 'ml' => '<mi>ln</mi>', 'type' => 'func'),
        array ('ascii' => 'det', 'ml' => '<mi>det</mi>', 'type' => 'func'),
        array ('ascii' => 'dim', 'ml' => '<mi>dim</mi>', 'type' => 'func'),
        array ('ascii' => 'mod', 'ml' => '<mi>mod</mi>', 'type' => 'func'),
        array ('ascii' => 'min', 'ml' => '<mi>min</mi>', 'type' => 'func', 'underover' => true),
        array ('ascii' => 'max', 'ml' => '<mi>max</mi>', 'type' => 'func', 'underover' => true),
        array ('ascii' => 'lim', 'ml' => '<mi>lim</mi>', 'type' => 'func', 'underover' => true),

        // Simple symbols
        array ('ascii' => '\'', 'ml' => '<mo>\'</mo>'), # single quote
        array ('ascii' => '!', 'ml' => '<mo>!</mo>'),
        array ('ascii' => 'O/', 'ml' => '<mo>&empty;</mo>'),
        array ('ascii' => 'del', 'ml' => '<mo>&part;</mo>'),
        array ('ascii' => 'grad', 'ml' => '<mo>&nabla;</mo>'),
        array ('ascii' => 'oo', 'ml' => '<mo>&infin;</mo>'),
        array ('ascii' => 'aleph', 'ml' => '<mo>&alefsym;</mo>'),
        array ('ascii' => '_|_', 'ml' => '<mo>&perp;</mo>'),
        array ('ascii' => 'TT', 'ml' => '<mo>&#x22A4;</mo>'),
        array ('ascii' => 'deg', 'ml' => '<mo>&deg;</mo>'),
        array ('ascii' => 'diamond', 'ml' => '<mo>&#x22C4;</mo>'),
        array ('ascii' => 'square', 'ml' => '<mo>&#x25A1;</mo>'),
        array ('ascii' => 'ldots', 'ml' => '<mo>&hellip;</mo>'),
        array ('ascii' => '...', 'ml' => '<mo>&hellip;</mo>'),
        array ('ascii' => 'cdots', 'ml' => '<mo>&#x22EF;</mo>'),
        array ('ascii' => 'vdots', 'ml' => '<mo>&vellip;</mo>'),
        array ('ascii' => 'ddots', 'ml' => '<mo>&#x22F1;</mo>'),
        array ('ascii' => 'CC', 'ml' => '<mi>&#x2102;</mi>'),
        array ('ascii' => 'NN', 'ml' => '<mi>&#x2115;</mi>'),
        array ('ascii' => 'QQ', 'ml' => '<mi>&#x211A;</mi>'),
        array ('ascii' => 'RR', 'ml' => '<mi>&#x211D;</mi>'),
        array ('ascii' => 'ZZ', 'ml' => '<mi>&#x2124;</mi>'),
        array ('ascii' => '(1/4)', 'ml' => '<mn>&frac14;</mn>'),
        array ('ascii' => '¼', 'ml' => '<mn>&frac14;</mn>'),
        array ('ascii' => '(1/2)', 'ml' => '<mn>&frac12;</mn>'),
        array ('ascii' => '½', 'ml' => '<mn>&frac12;</mn>'),
        array ('ascii' => '(3/4)', 'ml' => '<mn>&frac34;</mn>'),
        array ('ascii' => '¾', 'ml' => '<mn>&frac34;</mn>'),
        array ('ascii' => 'dx', 'ml' => '<mrow><mspace width="0.3em"/><mi>d</mi><mi>x</mi></mrow>'),  # denne form fremtvinger italics, og nbsp pynter
        array ('ascii' => 'dy', 'ml' => '<mrow><mspace width="0.3em"/><mi>d</mi><mi>y</mi></mrow>'),
        array ('ascii' => 'dz', 'ml' => '<mrow><mspace width="0.3em"/><mi>d</mi><mi>z</mi></mrow>'),
        array ('ascii' => 'dt', 'ml' => '<mrow><mspace width="0.3em"/><mi>d</mi><mi>t</mi></mrow>'),

        // Greek letters
        array ('ascii' => 'Alpha', 'ml' => '<mi>&Alpha;</mi>'),
        array ('ascii' => 'Beta', 'ml' => '<mi>&Beta;</mi>'),
        array ('ascii' => 'Gamma', 'ml' => '<mi>&Gamma;</mi>'),
        array ('ascii' => 'Delta', 'ml' => '<mo rspace="1px">&#x2206;</mo>'),   # NB!
        array ('ascii' => 'Epsilon', 'ml' => '<mi>&Epsilon;</mi>'),
        array ('ascii' => 'Zeta', 'ml' => '<mi>&Zeta;</mi>'),
        array ('ascii' => 'Eta', 'ml' => '<mi>&Eta;</mi>'),
        array ('ascii' => 'Theta', 'ml' => '<mi>&Theta;</mi>'),
        array ('ascii' => 'Iota', 'ml' => '<mi>&Iota;</mi>'),
        array ('ascii' => 'Kappa', 'ml' => '<mi>&Kappa;</mi>'),
        array ('ascii' => 'Lambda', 'ml' => '<mi>&Lambda;</mi>'),
        array ('ascii' => 'Mu', 'ml' => '<mi>&Mu;</mi>'),
        array ('ascii' => 'Nu', 'ml' => '<mi>&Nu;</mi>'),
        array ('ascii' => 'Xi', 'ml' => '<mi>&Xi;</mi>'),
        array ('ascii' => 'Omicron', 'ml' => '<mi>&Omicron;</mi>'),
        array ('ascii' => 'Pi', 'ml' => '<mi>&Pi;</mi>'),
        array ('ascii' => 'Rho', 'ml' => '<mi>&Rho;</mi>'),
        array ('ascii' => 'Sigma', 'ml' => '<mi>&Sigma;</mi>'),
        array ('ascii' => 'Tau', 'ml' => '<mi>&Tau;</mi>'),
        array ('ascii' => 'Upsilon', 'ml' => '<mi>&Upsilon;</mi>'),
        array ('ascii' => 'Phi', 'ml' => '<mi>&Phi;</mi>'),
        array ('ascii' => 'Chi', 'ml' => '<mi>&Chi;</mi>'),
        array ('ascii' => 'Psi', 'ml' => '<mi>&Psi;</mi>'),
        array ('ascii' => 'Omega', 'ml' => '<mi>&Omega;</mi>'),
        array ('ascii' => 'alpha', 'ml' => '<mi>&alpha;</mi>'),
        array ('ascii' => 'beta', 'ml' => '<mi>&beta;</mi>'),
        array ('ascii' => 'gamma', 'ml' => '<mi>&gamma;</mi>'),
        array ('ascii' => 'delta', 'ml' => '<mi>&delta;</mi>'),
        array ('ascii' => 'epsilon', 'ml' => '<mi>&epsilon;</mi>'),
        array ('ascii' => 'zeta', 'ml' => '<mi>&zeta;</mi>'),
        array ('ascii' => 'eta', 'ml' => '<mi>&eta;</mi>'),
        array ('ascii' => 'theta', 'ml' => '<mi>&theta;</mi>'),
        array ('ascii' => 'iota', 'ml' => '<mi>&iota;</mi>'),
        array ('ascii' => 'kappa', 'ml' => '<mi>&kappa;</mi>'),
        array ('ascii' => 'lambda', 'ml' => '<mi>&lambda;</mi>'),
        array ('ascii' => 'mu', 'ml' => '<mi>&mu;</mi>'),
        array ('ascii' => 'nu', 'ml' => '<mi>&nu;</mi>'),
        array ('ascii' => 'xi', 'ml' => '<mi>&xi;</mi>'),
        array ('ascii' => 'omicron', 'ml' => '<mi>&omicron;</mi>'),
        array ('ascii' => 'pi', 'ml' => '<mi>&pi;</mi>'),
        array ('ascii' => 'rho', 'ml' => '<mi>&rho;</mi>'),
        array ('ascii' => 'sigma', 'ml' => '<mi>&sigma;</mi>'),
        array ('ascii' => 'sigmaf', 'ml' => '<mi>&sigmaf;</mi>'),
        array ('ascii' => 'tau', 'ml' => '<mi>&tau;</mi>'),
        array ('ascii' => 'upsilon', 'ml' => '<mi>&upsilon;</mi>'),
        array ('ascii' => 'phi', 'ml' => '<mi>&phi;</mi>'),
        array ('ascii' => 'chi', 'ml' => '<mi>&chi;</mi>'),
        array ('ascii' => 'psi', 'ml' => '<mi>&psi;</mi>'),
        array ('ascii' => 'omega', 'ml' => '<mi>&omega;</mi>'),
        array ('ascii' => 'thetasym', 'ml' => '<mi>&thetasym;</mi>')

        );
        
        # Sortér så de største ascii-values står forrest, og dermed parses først
        usort ($this->syms, function($a, $b) { return (strlen($b['ascii']) - strlen($a['ascii'])); });
        # Udfyld tomme felter
        for($t = 0; $t < count($this->syms); $t++) { $this->syms[$t]['ml_nobr'] = $this->syms[$t]['ml']; }
    }

    function parse($prevsym, $situation) {
        # $situation er ass. array med følgende mulige boolean keys:
        #  'master' er true for den første parse() der kaldes af handle()
        #  'in_row' er true når parse() kaldes af parantes-subrutinen, dvs når parse() ikke kaldes for at indhente at argument til en operator
        #  'first_in_row' er true ved første kald i ovennævnte situation, dvs første symbol i parantesen
        #  'found_pipe' er true når parse() kaldes af parantes-subrutine der leder efter højre-pendant til left-pipe

        # Uddrag et token $sym af $this->parsestr
        if (! $situation['master']) {
            $this->parsestr = ltrim($this->parsestr);  # Fjern indledende whitespaces
            #error_log ('PARSE: ' . $this->parsestr);
            $sym = null;
            if ($this->parsestr != '') {
                # Test mod symboler i $this->syms, de længste først
                foreach ($this->syms as $s) {
                    if (strpos($this->parsestr, $s['ascii']) === 0) {
                        $this->parsestr = substr($this->parsestr, strlen($s['ascii']));
                        $sym = $s;
                        break;
                    }
                }
                # Tal (0-9 og punktum)
                if ((! isset($sym)) and (preg_match ('/^[0-9.]+/', $this->parsestr, $m))) {
                    $ml = $ml_nobr = '<mn>'.$m[0].'</mn>';
                    $sym = compact('ml', 'ml_nobr');
                    $this->parsestr = substr($this->parsestr, strlen($m[0]));
                }
                # Tekst i gåseøjne
                if ((! isset($sym)) and (preg_match ('/^"(.*?)"/', $this->parsestr, $m))) {
                    $ml = $ml_nobr = '<mtext>'.$m[1].'</mtext>';
                    $sym = compact('ml', 'ml_nobr');
                    $this->parsestr = substr($this->parsestr, strlen($m[0]));
                }
                # Ingen symboler matcher, så udtag første tegn
                if (! isset($sym)) {
                    $ml = $ml_nobr = '<mi>'.substr($this->parsestr, 0, 1).'</mi>';
                    $sym = compact('ml', 'ml_nobr');
                    $this->parsestr = substr($this->parsestr, 1);
                }
            } else {
                $ml = $ml_nobr = '<mspace />';
                $sym = compact('ml', 'ml_nobr');
            } 
            #error_log ('SYM: ' . $sym['ascii'] . ' -> ' . $sym['ml']);
        }

        # Paranteser
        #   Kalder parse() gentagne gange rekursivt for at lægge symboler ved siden af hinanden indtil right-bracket findes eller parsestr løber tør.
        #     Når parse-resultatet har 'from' sat, er der fundet en infix-operator, hvorfor sidstkomne element (arg1) er forældet og skal erstattes (arg1-infix-arg2).
        #     Derfor må jongleres med to variable ($old og $new) - for at sikre, at der først føjes til stakken når der ikke kommer korrektion tilbage.
        #   Om matrix-logik:
        #     Et udtryk som `((a,b),(c,d))` skal danne en matrix med a og b øverst, c og d nederst.
        #     Når parse() tager den første indre parantes fyldes $row med to elementer: 'a' og 'b'. Denne array returneres sammen med mathml for parantesen "(a,b)" til den kaldende parse().
        #     Når parse() tager den yderste parantes fyldes $row med '(a,b)' og '(c,d)', men dette er uvigtigt og bruges ikke.
        #       Derimod genkendes en matrix fordi hvert komma-adskilt element har sin egen ['row'] med samme antal elementer (2). Mathml-koden for disse samles i $matrix.

        if (($situation['master']) or ($sym['type'] == 'leftbracket') or (($sym['ascii'] == '|') and (! $situation['found_pipe']))) {
            $new = null; $old = $sym; $leftbracket = ($situation['master'] ? null : $sym); $rightbracket = null; $row = array(); $matrix = ''; $field = ''; $fields_in_row = 0;
            while (true) {
                $new = $this->parse($new, array('in_row' => true, 'first_in_row' => ($new === null), 'found_pipe' => ($leftbracket['ascii'] == '|')));
                if (! isset($new['from'])) {
                    # Føj til stakken, ellers vent til næste iteration (hvor $new bliver til $old)
                    $ml .= $old['ml'];
                    if (($old['type'] != 'leftbracket') and ($old['ascii'] != '|')) {
                        # Det fundne er hverken start- eller slutparantes (the meat of the bracket)
                        $ml_nobr .= $old['ml'];
                        if ($old['ascii'] == ',') {
                            if ($field == '') $matrix = false;
                            $row[] = $field;
                            $field = '';
                        } else {
                            $field .= $old['ml'];
                            if (($matrix !== false) and (isset($old['row'])) and (($fields_in_row == 0) or ($fields_in_row == count($old['row'])))) {
                                # Gyldig matrix kræver at hver row har samme antal fields
                                $fields_in_row = count($old['row']); # at den er 0 går kun een gang
                                $matrix .= '<mtr><mtd>' . implode('</mtd><mtd>', $old['row']) . '</mtd></mtr>';
                                #error_log ('ADDS TO MATRIX: ' . $fields_in_row . ' <mtr><mtd>' . implode('</mtd><mtd>', $old['row']) . '</mtd></mtr>');
                            } else {
                                $matrix = false;
                            }
                        }
                    }
                }
                $old = $new;
                if ((($old['type'] == 'rightbracket') or ($old['ascii'] == '|')) and (! $situation['master'])) { $rightbracket = $old; break; } # right bracket found
                if ($old['ml'] == '<mspace />') { $matrix = false; break; } # parsestr løber tør uden right bracket
            }
            # Afsporet af pipe, der ikke står som parantes begynd? I så fald push rightbracket tilbage på parsestr og abortér. Den skal parres på niveauet bagude.
            if (($leftbracket['ascii'] == '|') and ($rightbracket['ascii'] != '|')) {
                $this->parsestr = $rightbracket['ascii'] . $this->parsestr;
                $ml = $ml_nobr = $leftbracket['ml_op'] . $ml_nobr;
                return compact('ml', 'ml_nobr');
            }
            # Tilføj sidste field (før slutparantesen) til $row
            if ($field == '') $matrix = false; $row[] = $field; $field = '';
            # Ved gyldig matrix erstattes alt med denne.
            #  Kræver: Mindst 2 columns, ellers vil `f^((x))(t)` også tælle som matrix.
            if (($matrix !== false) and (count($row) > 1)) {
                $ml_nobr = '<mtable>' . $matrix . '</mtable>';
            }
            # Returner string med og uden paranteser
            $ml = '<mrow>' . $leftbracket['ml'] . $ml_nobr . $rightbracket['ml'] . '</mrow>';
            if (($leftbracket['ascii'] == '(') and ($rightbracket['ascii'] == ')')) {
                $ml_nobr = '<mrow>' . $ml_nobr . '</mrow>';
            } else {
                $ml_nobr = $ml;
            }
            #error_log ('PARANTES: (row: ' . count($row) . ') ' . $ml);
            return compact ('ml', 'ml_nobr', 'row');
        }

        # Unære operatorer
        if ($sym['type'] == 'unary') {
            $new = $this->parse($sym, null);
            $ml = $ml_nobr = $sym['wrap_left'] . $new['ml_nobr'] . $sym['wrap_right'];
            return compact ('ml', 'ml_nobr');
        }

        # Binære operatorer (uden infix, dvs kun root og stackrel)
        if ($sym['type'] == 'binary') {
            $new1 = $this->parse($sym, null);
            $new2 = $this->parse($new1, null);
            $ml = $ml_nobr = $sym['wrap_left'] . $new2['ml_nobr'] . $new1['ml_nobr'] . $sym['wrap_right'];
            return compact ('ml', 'ml_nobr');
        }

        # Standardfunktioner (som sin og cos, binder stærkt)
        if ($sym['type'] == 'func') {
            $old = $sym;
            $new = $this->parse($sym, null);
            while (isset ($new['from'])) { # fang sub- og superscripts til funktioner før selve argumentet
                $old = $new;
                $new = $this->parse($new, null);
            }
            $ml = $ml_nobr = '<mrow>' . $old['ml'] . $new['ml'] . '</mrow>';
            #error_log ('FUNKTION: ' . $ml);
            return compact ('ml', 'ml_nobr');
        }

        # Infix-operatorer
        if ($sym['type'] == 'infix') {
            if ($situation['first_in_row']) {
                # Ydertilfælde som `^x` og `_y`. Skal bare reddes.
                $ml = $ml_nobr = $sym['ml'];
                return compact ('ml', 'ml_nobr');
            }
            # Den simple stak er $prevsym, $sym og $new, svarende til de tre tegn i `y_z`.
            # Den udvidede stak er:
            $pos1 = $prevsym['from'][0];               #  `x    `
            $pos2 = $prevsym['from'][1];               #  ` ^   `
            $pos3 = $prevsym['from'][2];               #  `  y  `
            $pos4 = $sym;                              #  `   _ `
            $pos5 = $new = $this->parse($sym, null);   #  `    z`
            $op = $sym['ascii'];
            $bundtype = isset($prevsym['bundtype']) ? $prevsym['bundtype'] : $prevsym['type'];           # fx 'magnum' (går i arv)
            if ((($op == '_') or ($op == '^')) and (($pos2['ascii'] == '/') or ($pos2['ascii'] == '//'))) {
                # Tilfældet `x/y_z` eller `x/y^z`
                $ml = $pos2['wrap_left'] . $pos1['ml_nobr'] . $pos4['wrap_left'] . $pos3['ml'] . $pos5['ml_nobr'] . $pos4['wrap_right'] . $pos2['wrap_right'];
            } elseif (($op == '^') and ($pos2['ascii'] == '_') and ($pos1['underover'])) {
                # Tilfældet `prod_y^z`
                $ml = '<munderover>' . $pos1['ml'] . $pos3['ml_nobr'] . $pos5['ml_nobr'] . '</munderover>';
            } elseif (($op == '_') and ($pos2['ascii'] == '^') and ($pos1['underover'])) {
                # Tilfældet `prod^y_z`
                $ml = '<munderover>' . $pos1['ml'] . $pos5['ml_nobr'] . $pos3['ml_nobr'] . '</munderover>';
            } elseif (($op == '_') and ($prevsym['underover'])) {
                # Tilfældet `prod_z`
                $ml = '<munder>' . $prevsym['ml'] . $new['ml_nobr'] . '</munder>';
            } elseif (($op == '^') and ($prevsym['underover'])) {
                # Tilfældet `prod^z`
                $ml = '<mover>' . $prevsym['ml'] . $new['ml_nobr'] . '</mover>';
            } elseif (($op == '^') and ($pos2['ascii'] == '_')) {
                # Tilfældet `x_y^z`  (eneste som asciimathml.js genkender)
                $ml = '<msubsup>' . $pos1['ml'] . $pos3['ml_nobr'] . $pos5['ml_nobr'] . '</msubsup>';
            } elseif (($op == '_') and ($pos2['ascii'] == '^')) {
                # Tilfældet `x^y_z`  (kommer ud på det samme)
                $ml = '<msubsup>' . $pos1['ml'] . $pos5['ml_nobr'] . $pos3['ml_nobr'] . '</msubsup>';
            } elseif (($op == '_') or ($op == '^')) {
                # Tilfældet `y_z` eller `y^z`
                $ml = $sym['wrap_left'] . $prevsym['ml'] . $new['ml_nobr'] . $sym['wrap_right'];
            } elseif (($op == '/') or ($op == '//')) {
                $ml = $sym['wrap_left'] . $prevsym['ml_nobr'] . $new['ml_nobr'] . $sym['wrap_right'];
            } else msg ('Dunno how I got here!');
            $from = array($prevsym, $sym, $new);
            $ml_nobr = $ml;
            return compact ('ml', 'ml_nobr', 'from', 'bundtype');
        }

        # Signs (minus og plusminus)
        if ($sym['type'] == 'sign') {
            if (($situation['first_in_row']) or (! $situation['in_row']) or ($prevsym['type'] == 'op') or ($prevsym['bundtype'] == 'magnum')) {
                # Brug fortegnsversionen af symbolet i tilfælde hvor 1) symbolet optræder allerførst i parantes/globalt, 2) symbolet optræder som argument til operator (!in_row),
                # 3) symbolet optræder efter simpel inline-operator, eller 4) efter infix-symbolkæde med bundtype magnum (dvs i praksis magnum-operator med index)
                $new = $this->parse($sym, null);
                $ml = $ml_nobr = '<mrow>' . $sym['ml_sign'] . $new['ml'] . '</mrow>';
                if ($new['ml'] != '<mspace />') return compact ('ml', 'ml_nobr');
            }
            return $sym;
        }

        # Løsslupne right brackets - puttes tilbage til næste parserunde, mens denne returnerer tom streng  (fx `(4/)_x`)
        if (($sym['type'] == 'rightbracket') and (! $situation['in_row'])) {
            #error_log ('LOES!');
            $this->parsestr = $sym['ascii'] . $this->parsestr;
            $ml = $ml_nobr = '<mspace />';
            return compact ('ml', 'ml_nobr');
        }

        # Andre symboler
        return ($sym);
    }

    function handle($match, $state, $pos, &$handler){
        if (count($this->syms) == 0) $this->setup_syms();
        $this->parsestr = substr ($match, 1, -1);
        if (substr ($match, 1, 1) == ' ') { $displaystyle = 'true'; } else { $displaystyle = 'false'; }
        #error_log ('------ HANDLE -------');
        $parsed = $this->parse(null, array('master' => true));
        $return = '<math xmlns="http://www.w3.org/1998/Math/MathML" title="'.htmlentities ($match).'" displaystyle="'.$displaystyle.'">'.$parsed['ml'].'</math>';
        # HER FØLGER TO NØDLØSNINGER SPECIFIKT TIL MORTEN:
        $return = str_replace('<mi>', '<mi mathvariant=sans-serif-italic>', $return);               // i nyere tid eneste måde at få sans-serif!
        $return = '<span pos="' . $pos . '" len="' . strlen($match) . '">' . $return . '</span>';   // wrappes i span aht mdblclick
	    return $return;
         
	}

    function render($mode, &$renderer, $data) {
        if ($mode!='xhtml') return false;
        $renderer->doc .= $data;
        return true;
    }
}
?>
