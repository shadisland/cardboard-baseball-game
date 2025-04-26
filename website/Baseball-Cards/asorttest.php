<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sortable Grid</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        #sortable {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 300px;
        }
        #sortable li {
            margin: 5px 0;
            padding: 10px;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            cursor: move;
        }
        #status {
            margin-top: 20px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>
    <h1>Sortable Names</h1>
    <p>Drag and drop the names to reorder them. The order will be saved automatically.</p>

    <ul id="sortable">
        <li data-id="1">Alice</li>
        <li data-id="2">Bob</li>
        <li data-id="3">Charlie</li>
        <li data-id="4">Diana</li>
        <li data-id="5">Eve</li>
    </ul>

    <div id="status"></div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $(function () {
            // Make the list sortable
            $("#sortable").sortable({
                update: function (event, ui) {
                    // Get the new order
                    let order = $("#sortable li").map(function () {
                        return $(this).data("id");
                    }).get();

                    // Send the new order via Ajax
                    $.ajax({
                        url: "save_batting_order.php",
                        method: "POST",
                        data: { order: order },
                        dataType: "json",
                        success: function (response) {
                            if (response.status === "success") {
                                $("#status").text(response.message);
                            } else {
                                $("#status").text("Failed to save order. Try again.");
                            }
                        },
                        error: function () {
                            $("#status").text("An error occurred. Please try again.");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>