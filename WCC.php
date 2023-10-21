<?php

require 'config.php';




if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sampleCode = $_POST["sample_code"];



    //removing comments
    $single = preg_replace('![ \t]*//.*[ \t]*[\r\n]!', '', $sampleCode);
    $multiple = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#', '', $single);
    $excess = preg_replace('/\s+/', ' ', $multiple);
    $trim = trim($excess, " ");
    //semicolns break
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

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>WCC Calculations</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">




</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php

        require 'components/sidenav.php';

        ?>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php

                require 'components/header.php';

                ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">WCC Calculations</h1>
                        <!-- <a href="javascript:void(0);" id="generatePdfButton"
                            class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> -->

                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js">

                            function generatePDF() {
                                // Create a new jsPDF instance
                                var doc = new jsPDF();

                                // Get the table element by its ID or any other method
                                var table = document.getElementById('dataTable'); // Change 'yourTableId' to your table's ID
                                console.log(table);
                                // Convert the table to a canvas
                                html2canvas(table).then(function (canvas) {
                                    var imgData = canvas.toDataURL('image/png');

                                    // Add the canvas as an image to the PDF
                                    doc.addImage(imgData, 'PNG', 10, 10, 180, 0);

                                    // Save the PDF file (you can customize the filename)
                                    doc.save('report.pdf');
                                });
                            }

                            // Add an event listener to the button
                            document.getElementById('generatePdfButton').addEventListener('click', generatePDF);

                        </script>
                    </div>



                    <div class="row">

                        <!-- DataTales Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">WCC Calculations for your code is given
                                    bellow</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Line No</th>
                                                <th>Executable Line</th>
                                                <th>W<sp>I<sp>(Inheritence)</th>
                                                <th>W<sp>N<sp>(Nesting)</th>
                                                <th>W<sp>C<sp>(Control Structure)</th>
                                                <th>S(Size)</th>
                                                <th>W<sp>t</sp>
                                                <th>S*W<sp>t</sp>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tfoot>
                                            <tr>
                                                <th>Line No</th>
                                                <th>Executable Line</th>
                                                <th>W<sp>I<sp>(Inheritence)</th>
                                                <th>W<sp>N<sp>(Nesting)</th>
                                                <th>W<sp>C<sp>(Control Structure)</th>
                                                <th>S(Size)</th>
                                                <th>W<sp>t</sp>
                                                <th>S*W<sp>t</sp>
                                                </th>
                                            </tr>
                                        </tfoot>
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
                                        foreach ($split as $line) {


                                            //size calculations-->
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
                                            $operators = array('+', '-', '*', '/', '%', '=', '>', '<', '&&', '!', '|', '^', '~', ',', '.', '::', '<<', '>>', '==', '!=', '&&', '||', '<=', '>=', '++', '--', '()');
                                            foreach ($operators as $op) {
                                                if (stripos($line, $op) !== false) {
                                                    // Use stripos for a case-insensitive search
                                                    $count++;
                                                }
                                            }

                                            if (preg_match('/^\s*}\s*$/', $line))
                                                $count = 0;

                                            //indentify functions
                                            if (preg_match_all('/\b(public:void)\b/', $line))
                                                $count = $count + 1;


                                            ?>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <?php echo $l + 1 ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $line ?>
                                                    </td>


                                                    <td>
                                                        <?php echo $ci ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $cc ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $cn ?>
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

                                                //inheritence
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

                                                //control structure check--->
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

                                                //inhertitence---->
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

                                                //nesting ===>
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
                                            <td></td>
                                            <td>";
                                        echo $tot;
                                        echo "</td>
    </tr>"
                                            ?>

                                        </tbody>

                                    </table>
                                    <a onclick="goBack()" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fa-solid fa-arrow-left fa-beat" style="color: #f5f7fa;"></i>
                                        </span>
                                        <span class="text">Go Back</span>
                                    </a>

                                    <script>
                                        function goBack() {
                                            window.history.back();
                                        }
                                    </script>
                                </div>
                            </div>

                        </div>


                    </div>
                    <?php
                    $sql = "INSERT INTO wcc(code,total) values('$sampleCode','$tot');";
                    if ($conn->query($sql) === TRUE) {

                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }

                    ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php
            require 'components/footer.php';
            ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>