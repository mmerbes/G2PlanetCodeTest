<?php
    include('CsvImporter.php');
    if (!empty($_GET['q'])) {
        switch ($_GET['q']) {
            case 'info':
                phpinfo(); 
                exit;
                break;
        }
    }

    
    $filename = "restaurants.csv";
    $importer = new CsvImporter("./restaurants.csv", true, ",");
    $data = $importer->get(); 
    //print_r($data);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Anything at All</title>
        <link rel="stylesheet" href="./main.css" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Karla:400" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="form-group row">
                    <label for="search-input" class="col-2 col-form-label">Search</label>
                    <div class="col-10">
                        <input class="form-control" type="search" id="tableFilter" placeholder="Search for resurant">
                    </div>
                </div>
                <table class="table table-hover" id="resturantTable">
                    <thead>
                        <tr class="thead-default">
                            <th>Restaurant Name</th>
                            <th>Cuisine Type</th>
                        </tr>
                    </thead>
                    <tbody id="rBody">
                        <?php foreach($data as $row) {
                            echo "<tr>
                                <td>{$row['restaurant_name']}</td>
                                <td>{$row['cuisine_type']}</td>
                            </tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script src="./filter.js" type="text/javascript"></script>
    </body>
</html>