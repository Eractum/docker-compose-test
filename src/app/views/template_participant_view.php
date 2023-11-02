<html>
<head>
    <title>Hello...</title>

    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../public/css/participant.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <?php

    if (isset($data)) {

        if (is_array($data) && is_array(current($data))) {
            ?>
            <script type="text/javascript">
                var dbData = <?php echo json_encode($data); ?>;
                //alert(dbData[0]['firstname']);
                var rowData = [];

                for (let i=0; i < dbData.length; i++) {
                    let manager = dbData[i]['parent_id'].toString();
                    if (dbData[i]['parent_id'] === 0) {
                        manager = '';
                    }

                    let timeStamp = dbData[i]['start_date'];
                    let dateFormat = new Date(timeStamp * 1000);

                    rowData[i] = [
                        {
                            'v':dbData[i]['entity_id'].toString(),
                            'f':dbData[i]['firstname'].toString() +
                                ' ' +
                                dbData[i]['lastname'] +
                                '<div class="'+dbData[i]['position']+'" >' +
                                '<img src="../public/img/' + dbData[i]['position'] + '.svg">' +
                                dbData[i]['position'] +
                                '</div><div class="collapsable" style="display: none">'+
                                'Mail To: ' +
                                dbData[i]['mailto'] +
                                "<br>" +
                                'Shares: ' +
                                dbData[i]['shares_amount'] +
                                "<br>" +
                                'Start Date: ' +
                                dateFormat.getDate() + '.' + (dateFormat.getMonth()+1) + '.' + dateFormat.getFullYear()+
                                '</div>'
                        },
                        manager,
                        'Mail To: ' +
                        dbData[i]['mailto'] +
                        "\n" +
                        'Shares: ' +
                        dbData[i]['shares_amount'] +
                        "\n" +
                        'Start Date: ' +
                        dateFormat.getDate() + '.' + (dateFormat.getMonth()+1) + '.' + dateFormat.getFullYear()
                    ]
                }

                google.charts.load('current', {packages:["orgchart"]}).then(function (){
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Name');
                    data.addColumn('string', 'Manager');
                    data.addColumn('string', 'ToolTip');

                    data.addRows(rowData);

                    var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
                    chart.draw(data, {'allowHtml':true});

                    var containers = document.getElementsByClassName('google-visualization-orgchart-node');

                    for(let j=0; j<containers.length; j++) {
                        containers[j].addEventListener('click', function (e) {
                            e.preventDefault();
                            for (let i = 0; i < this.children.length; i++) {
                                let isHidden = window.getComputedStyle(this.children[i]).getPropertyValue("display") == 'none';

                                if (this.children[i].classList.contains('collapsable')) {
                                    this.children[i].style.setProperty('display', isHidden ? 'block' : 'none');
                                }
                            }
                        }, false);
                    }
                });
            </script>
            <?php
        }
    }
    ?>

</head>
<body>
<?php include 'app/views/'.$content_view; ?>
</body>
</html>