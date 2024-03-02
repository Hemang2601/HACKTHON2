<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Certificate</title>
    <link rel="stylesheet" href="css/upload.css">
</head>

<body>

    <?php
    $pageTitle = "Upload Certificate";
    include('header.php');
    ?>

<div class="container">
        <form action="uploadprocess.php" method="post" enctype="multipart/form-data">
            
            <div class="input-group">
                <label for="organization">
                    <span class="icon"><i class="fas fa-building"></i></span>
                    Organization Name:
                </label>
                <input type="text" name="organization" id="organization" required>
            </div>

            <div class="input-group">
                <label for="category">
                    <span class="icon"><i class="fas fa-list-alt"></i></span>
                    Category:
                </label>
                <select name="category" id="category" required>
                    <option>Select Category</option>
                    <option value="SPORTS">Sports</option>
                    <option value="EDUCATION">Education</option>
                    <option value="CULTURE">Culture</option>
                    <option value="OTHER">Other</option>
                </select>
            </div>

            <div class="input-group">
                <label for="certificate_name">
                    <span class="icon"><i class="fas fa-certificate"></i></span>
                    Certificate Name:
                </label>
                <input type="text" name="certificate_name" id="certificate_name" required>
            </div>

            <div class="input-group">
                <label for="date">
                    <span class="icon"><i class="far fa-calendar-alt"></i></span>
                    Date:
                </label>
                <input type="date" name="date" id="date" required>
            </div>

            <div class="input-group">
                <label for="file">
                    <span class="icon"><i class="fas fa-file-upload"></i></span>
                    Select certificate:
                </label>
                <input type="file" name="file" id="file" accept="image/*" required>
            </div>

            <button type="submit">
                <span class="icon"><i class="fas fa-upload"></i></span>
                Upload Certificate
            </button>
        </form>
    </div>

    <?php include('footer.php'); ?>

</body>

</html>
