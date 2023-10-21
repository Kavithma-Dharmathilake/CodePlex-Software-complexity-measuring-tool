<?php

require 'config.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sampleCode = $_POST["sample_code"];

    $single = preg_replace('![ \t]*//.*[ \t]*[\r\n]!', '', $sampleCode);
    $multiple = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#', '', $single);
    $excess = preg_replace('/\s+/', ' ', $multiple);
    $trim = trim($excess, " ");
    $for_semicolon = preg_replace_callback(
        /** @lang text */    '~\b(?:while|for)\s*(\((?:[^()]++|(?1))*\))~u',
        static function ($m) {
            return str_replace(';', ';', $m[0]);
        },
        $trim
    );
    $split = preg_split('/(?<=[;{}])/', $for_semicolon, 0, PREG_SPLIT_NO_EMPTY);



    //finding inheritence

    $i = 0;
    $class_arr = [];
    foreach ($split as $line) {
        $pattern = '/class (\w+)/';
        if (@preg_match($pattern, $line, $matches)) {
            $class_arr[$i++] = $matches[1];
        }

    }

}
?>

<table border="1">
    <th> line</th>
    <th>Wi</th>
    <th>Wc</th>
    <th>Wn</th>
    <th>S</th>
    <th>Wt</th>
    <th>WCC</th>
    <?php
    $array_inherit = [];
    $array_control = [];
    $array_nesting = [];
    $array_size = [];

    $l = 0;
    $c1 = 0;
    $c2 = 0;
    $c3 = 0;
    $c4 = 0;

    $ci = 0;
    $cn = 0; //control structures
    $cc = 0; //nesting
    $count = 0;
    $total = 0;

    $conditional_words = array('if', 'for', 'while', 'switch', 'case');
    foreach ($split as $line) { ?>
        <tr>
            <td>
                <?php echo $line ?>
            </td>


            <td>
                <?php echo $ci ?>
            </td>
            <td>
                <?php echo $cn ?>
            </td>
            <td>
                <?php echo $cc ?>
            </td>
            <td>
                <?php echo $count ?>
            </td>
            <td>
                <?php echo $ci + $cn + $cc ?>
            </td>
            <td>
                <?php echo $count * ($ci + $cn + $cc) ?>
            </td>
        </tr>

        <?php
        $array_inherit[$l++] = $ci;
        $array_control[$c1++] = $cn;
        $array_nesting[$c2++] = $cc;
        $array_nesting[$c4++] = $count;
        $array_total[$c3++] = $count * ($ci + $cn + $cc);

        if ($ci == 0) {
            $class_arr1 = $class_arr;
            foreach ($class_arr1 as $cl1) {
                if (preg_match('/\b' . $cl1 . '\b/', $line)) {
                    $ci = 1;
                    $index = array_search($cl1, $class_arr1);
                    if ($index !== false) {
                        unset($class_arr1[$index]);
                    }
                    foreach ($class_arr1 as $cl2) {
                        if (preg_match('/\b' . $cl2 . '\b/', $line)) {
                            $ci = 2;
                            $index = array_search($cl2, $class_arr1);
                            if ($index !== false) {
                                unset($class_arr1[$index]);
                            }
                            foreach ($class_arr1 as $cl3) {
                                if (preg_match('/\b' . $cl3 . '\b/', $line)) {
                                    $ci = 3;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                    break;
                }
            }
        }

        if ($cn == 0) {
            foreach ($conditional_words as $word) {
                if (preg_match('/\b' . $word . '\b/', $line)) {
                    if ($word === 'if') {
                        $cn = 1;

                    } elseif ($word === 'for' || $word === 'while') {
                        $cn = 2;

                    }
                    break;
                }
            }

        } else {
            if (preg_match('/^\s*}\s*$/', $line))
                $cn = 0;

        }
        if ($ci != 0) {

            if (preg_match('/^\s*;\s*$/', $line))
                $ci = 0;
            if (preg_match('/^\s*}\s*$/', $line))
                $ci = 0;
            if (preg_match('/^\s*int main(){\s*$/', $line))
                $ci = 0;
            if (preg_match('/^\s*return 0;\s*$/', $line))
                $ci = 0;


        }

        if ($cc == 0) {
            if (preg_match('/\b(for|while|if)\b/', $line)) {
                $cc = 1;
            }
        } elseif ($cc == 1) {
            if (preg_match('/\b(for|while|if)\b/', $line)) {
                $cc = 2;
            }

        }
        if ($cc != 0) {
            if (preg_match('/^\s*}\s*$/', $line))
                $cc = 0;
        }

        //size-->
        $count = 0;
        //count number of string literals
        $pattern = '/"(?:[^"\\\\]|\\\\.)*"/';
        if (preg_match_all($pattern, $line, $matches)) {
            $count = $count + count($matches[0]);
        }

        //count keywords
        if (preg_match_all('/\b(int|cout|endl|double|float|break|catch|class|const|continue|default|do|else|enum|final|private|protected|public|return|void)\b/', $line)) {
            $count++;
        }
        //count operators
        $operators = array('+', '-', '*', '/', '%', '=', '>', '<', '&&', '!', '|', '^', '~', ',', '.', '::', '<<', '>>', '==', '!=', '&&', '||', '<=', '>=', '++', '--');
        foreach ($operators as $op) {
            if (stripos($line, $op) !== false) {
                // Use stripos for a case-insensitive search
                $count++;
            }
        }

        if (preg_match('/^\s*}\s*$/', $line))
            $count = 0;


        ?>

    <?php }
    print_r($array_total);
    $tot = 0;
    foreach ($array_total as $n) {
        $tot = $tot + $n;
    }
    echo "<tr>
        <td>Total weight</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>";
    echo $tot;
    echo "</td>
    </tr>"
        ?>
</table>


<?php












?>