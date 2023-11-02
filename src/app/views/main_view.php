
<div class="container">
    <a href="/Participant/Generate">Generate 99 users</a>
    <a href="/Participant/Reset">Reset Participant</a>
</div>
    <?php

    if (isset($data)) { ?>
        <div class="container">
    <?php
        if (is_array($data) && is_array(current($data))) {
            echo '<table class="table table-striped">';
            echo '<thead><tr><th></th>';



            echo '</tr></thead>';

            foreach ($data as $value) {
                echo '<tr>';
                echo '<td><a href="#"><span class="glyphicon glyphicon-search"></span></a></td>';
                foreach ($value as $element) {
                    echo '<td>' . $element . '</td>';
                }

                echo '</tr>';
            }
            echo '</table>';
        }
        elseif (is_string($data)) echo $data;



    ?>
</div>
<?php
    }