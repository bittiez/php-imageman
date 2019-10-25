<?php
#######
#Variables
#######
$img_dir = "imgs/";
$extension = array("jpeg", "jpg", "png");
$counter_file = "counter.txt";

#######
#Check for ajax delete image
#######
if(isset($_GET['del'])){
    //do stuff
    unlink($img_dir . $_GET['del']);
    exit;
}

#######
#If a file has been uploaded
#######
if (isset($_FILES['image'])) {
    $counterVal = incrementCounter($counter_file);
    $errors = array();
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_type = $_FILES['image']['type'];
    $file_ext = getFileExt($_FILES['image']['name']);


    if (in_array($file_ext, $extension) === false) {
        $errors[] = "extension not allowed, only the following image types are allowed: " . join(", ", $extension);
    }
    if (empty($errors) == true) {
        move_uploaded_file($file_tmp, $img_dir . $counterVal . "." . $file_ext);
        echo "Image uploaded!";
    } else {
        print_r($errors);
    }
}

#######
#Get list of images in folder
#######
$file_list = scandir($img_dir);
$image_list = array();
foreach ($file_list as $value) {
    if (is_dir($img_dir . $value)) {
        continue;
    }
    $t_extension = getFileExt($value);
    if (in_array($t_extension, $extension)) {
        $image_list[] = $value;
    }
}

#######
#Functions
#######

function getFileExt($file) {
    $file_ext = explode('.', $file);
    $file_ext = end($file_ext);
    $file_ext = strtolower($file_ext);
    return $file_ext;
}

function incrementCounter($counter_file) {
    if (!file_exists($counter_file)) {
        $f = fopen($counter_file, "w");
        fwrite($f, "0");
        fclose($f);
    }
    $f = fopen($counter_file, "r+");
    $counterVal = fread($f, filesize($counter_file));
    $counterVal++;
    fseek($f, 0);
    fwrite($f, $counterVal);
    fclose($f);
    return $counterVal;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <link href="bootstrap.min.css" rel="stylesheet">
        <title>Image Manager</title>
    </head>
    <body style="height: 100vh;">
        <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="submit" id="inputGroupFileAddon01" class="btn btn-primary">Upload</button>
                </div>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="image"
                           aria-describedby="inputGroupFileAddon01" name="image" accept="<?php echo "." . join(",.", $extension); ?>">
                    <label class="custom-file-label" for="image" id="labelForInput" aria-describedby="image">Choose file</label>
                </div>
            </div>
        </form>
        <?php
        $image_lists_broken = array();
        $i = 0;
        $i_list = 0;
        foreach ($image_list as $value) {
            $image_lists_broken[$i_list][] = $value;
            $i++;
            if ($i >= 3) {
                $i_list++;
                $i = 0;
            }
        }
        foreach ($image_lists_broken as $t_img_list) {
            ?>
            <div class="container">
                <div class="row">
                    <?php
                    foreach ($t_img_list as $value) {
                        ?>
                        <div class="col">
                            <img autocomplete="off" src="<?php echo $img_dir . $value; ?>" alt="" class="img-thumbnail img-responsive img-fluid">
                            <button autocomplete="off" id="" class="btn btn-md btn-danger w-100" onclick="delImage('<?php echo $value; ?>', this)">DELETE</button>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>

        <script type="text/javascript" src="bootstrap.min.js"></script>
        <script type="text/javascript" src="jquery.js"></script>
        <script type="text/javascript">
                                $('#image').on('change', function () {
                                    //get the file name
                                    var fileName = $(this).val();
                                    //replace the "Choose a file" label
                                    $(this).next('.custom-file-label').html(fileName);
                                });
                                function delImage(image, button) {
                                     $.get("?del=" + image, function(data, status){
                                        $(button).html("DELETED");
                                        $(button).addClass("btn-success");
                                        $(button).removeClass("btn-danger");
                                        $(button).prop("disabled",true);
                                      });
                                }
        </script>
    </body>
</html>
