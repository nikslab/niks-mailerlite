<?php

/**
 * Just shows README.md as a web page
 * 
 * PHP Version 8.0
 * 
 * @author Nik Stankovic <niks.work.goog@gmail.com>
 * @link   https://github.com/nikslab/niks-mailerlite
 */


$markdown = file_get_contents("../README.md");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Markdown Viewer</title>
</head>
<body>
    <div id="markdown-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/showdown@2.0.0/dist/showdown.min.js">
    </script>
    <script>
        const converter = new showdown.Converter();
        const markdown = <?php echo json_encode($markdown); ?>;
        document.getElementById('markdown-container').innerHTML = 
            converter.makeHtml(markdown);
    </script>
</body>
</html>