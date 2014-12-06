<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A visualizer for parsing string
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->libdir.'/accesslib.php');
require_once($CFG->dirroot.'/lib/classes/text.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');

global $USER;

require_login();

$url = new moodle_url('/blocks/formal_langs/parsertests/visualize.php', array());

require_login();
$PAGE->requires->jquery();
$PAGE->set_url($url);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');


$heading = "Visualize Parsing";

$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->navbar->add($heading);

echo $OUTPUT->header();
echo "<script type='text/javascript' src='http://gojs.net/latest/release/go.js'></script>";
echo "<style type='text/css'>";
echo ".diagramContainer {
  border : solid 1px blue;
}";
echo "</style>";
echo "<div style='margin-bottom: 30px;'>";
    echo "<input type='text' style='width: 400px; margin-right: 20px;'  name='text'  />";
    echo "<input id='visualize' type='button' name='submit' value='submit'>";
echo "</div>";
echo "<div>";
?>
    <div id="sample">
        <div id="myDiagram" style="background-color: white; border: solid 1px black; width: 100%; height: 500px"></div>
    </div>
<?
echo "</div>";
?>
    <script type="text/javascript">
        var myDiagram = null;
        function init_tree(data) {
            var $ = go.GraphObject.make;  // for conciseness in defining templates
            //if (myDiagram == null) {
                myDiagram =
                    $(go.Diagram, "myDiagram",  // must be the ID or reference to div
                        {
                            allowCopy: false,
                            layout:  // create a TreeLayout for the family tree
                                $(go.TreeLayout,
                                    { angle: 90, nodeSpacing: 5 })
                        });

                var bluegrad = $(go.Brush, go.Brush.Linear, { 0: "rgb(60, 204, 254)", 1: "rgb(70, 172, 254)" });
                var pinkgrad = $(go.Brush, go.Brush.Linear, { 0: "rgb(255, 192, 203)", 1: "rgb(255, 142, 203)" });


                // get tooltip text from the object's data
                function tooltipTextConverter(person) {
                    var str = "";
                    str += "Born: " + person.birthYear;
                    if (person.deathYear !== undefined) str += "\nDied: " + person.deathYear;
                    if (person.reign !== undefined) str += "\nReign: " + person.reign;
                    return str;
                }



                // replace the default Node template in the nodeTemplateMap
                myDiagram.nodeTemplate =
                    $(go.Node, "Auto",
                        { deletable: false },
                        new go.Binding("text", "name"),
                        $(go.Shape, "Rectangle",
                            { fill: "lightgray",
                                stroke: "black",
                                stretch: go.GraphObject.Fill,
                                alignment: go.Spot.Center }),
                        $(go.TextBlock,
                            { font: "bold 8pt Helvetica, bold Arial, sans-serif",
                                alignment: go.Spot.Center,
                                margin: 6 },
                            new go.Binding("text", "name"))
                    );

                // define the Link template
                myDiagram.linkTemplate =
                    $(go.Link,  // the whole link panel
                        { routing: go.Link.Orthogonal, corner: 5, selectable: false },
                        $(go.Shape));  // the default black link shape
            //}
            // create the model for the family tree
            myDiagram.model = new go.TreeModel(data);

        }
        $(document).ready(function() {
            $('#visualize').click(function() {
                $.ajax({
                    'url': '<?=$CFG->wwwroot?>/blocks/formal_langs/parsertests/visualizebackend.php',
                    'type': 'POST',
                    'data': {
                        'text' : $("input[name=text]").val()
                    },
                    'dataType': 'json',
                    'success': function(data) {
                        $("#myDiagram").remove();
                        $("#sample").html("<div id=\"myDiagram\" style=\"background-color: white; border: solid 1px black; width: 100%; height: 500px\"></div>");
                        init_tree(data);
                    }
                });
            })
        })
    </script>
<?
echo $OUTPUT->footer();