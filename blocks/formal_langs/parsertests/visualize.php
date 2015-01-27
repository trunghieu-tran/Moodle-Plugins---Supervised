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
?>
<script src="http://d3js.org/d3.v3.min.js"></script>
<style type='text/css'>
.diagramContainer {

}
.node {
    cursor: pointer;
}

.overlay{
    background-color:#EEE;
}

.node circle {
    fill: #fff;
    stroke: steelblue;
    stroke-width: 1.5px;
}

.node text {
    font-size:10px;
    font-family:sans-serif;
}

.link {
    fill: none;
    stroke: #ccc;
    stroke-width: 1.5px;
}

.templink {
    fill: none;
    stroke: red;
    stroke-width: 3px;
}

.ghostCircle.show{
    display:block;
}

.ghostCircle, .activeDrag .ghostCircle{
    display: none;
}

.input-container {
    margin-bottom: 30px;
}

div div .source-text {
    width: 800px;
    margin-right: 20px;
}
</style>
<div class="input-container">
    <input type='text' class="source-text"  name='text'  />
    <input id='visualize' type='button' name='submit' value='submit'>
</div>
<div id="sample">
    <div id="myDiagram" ></div>
</div>
<script type="text/javascript">
    var myDiagram = null;
    // Taken from http://bl.ocks.org/robschmuecker/7880033
    function init_tree(treeData) {
        // Calculate total nodes, max label length
        var totalNodes = 0;
        var maxLabelLength = 0;
        // variables for drag/drop
        var selectedNode = null;
        var draggingNode = null;
        // panning variables
        var panSpeed = 200;
        var panBoundary = 20; // Within 20px from edges will pan when dragging.
        // Misc. variables
        var i = 0;
        var duration = 750;
        var root;

        // size of the diagram
        var viewerWidth = $(document).width();
        var viewerHeight = $(document).height();

        var tree = d3.layout.tree()
            .size([viewerHeight, viewerWidth]);

        // define a d3 diagonal projection for use by the node paths later on.
        var diagonal = d3.svg.diagonal()
            .projection(function(d) {
                return [d.y, d.x];
            });

        // A recursive helper function for performing some setup by walking through all nodes

        function visit(parent, visitFn, childrenFn) {
            if (!parent) return;

            visitFn(parent);

            var children = childrenFn(parent);
            if (children) {
                var count = children.length;
                for (var i = 0; i < count; i++) {
                    visit(children[i], visitFn, childrenFn);
                }
            }
        }

        // Call visit function to establish maxLabelLength
        visit(treeData, function(d) {
            totalNodes++;
            maxLabelLength = Math.max(d.name.length, maxLabelLength);

        }, function(d) {
            return d.children && d.children.length > 0 ? d.children : null;
        });


        // TODO: Pan function, can be better implemented.

        function pan(domNode, direction) {
            var speed = panSpeed;
            if (panTimer) {
                clearTimeout(panTimer);
                translateCoords = d3.transform(svgGroup.attr("transform"));
                if (direction == 'left' || direction == 'right') {
                    translateX = direction == 'left' ? translateCoords.translate[0] + speed : translateCoords.translate[0] - speed;
                    translateY = translateCoords.translate[1];
                } else if (direction == 'up' || direction == 'down') {
                    translateX = translateCoords.translate[0];
                    translateY = direction == 'up' ? translateCoords.translate[1] + speed : translateCoords.translate[1] - speed;
                }
                scaleX = translateCoords.scale[0];
                scaleY = translateCoords.scale[1];
                scale = zoomListener.scale();
                svgGroup.transition().attr("transform", "translate(" + translateX + "," + translateY + ")scale(" + scale + ")");
                d3.select(domNode).select('g.node').attr("transform", "translate(" + translateX + "," + translateY + ")");
                zoomListener.scale(zoomListener.scale());
                zoomListener.translate([translateX, translateY]);
                panTimer = setTimeout(function() {
                    pan(domNode, speed, direction);
                }, 50);
            }
        }

        // Define the zoom function for the zoomable tree

        function zoom() {
            svgGroup.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
        }


        // define the zoomListener which calls the zoom function on the "zoom" event constrained within the scaleExtents
        var zoomListener = d3.behavior.zoom().scaleExtent([0.1, 3]).on("zoom", zoom);


        // define the baseSvg, attaching a class for styling and the zoomListener
        var baseSvg = d3.select("#myDiagram").append("svg")
            .attr("width", viewerWidth)
            .attr("height", viewerHeight)
            .attr("class", "overlay")
            .call(zoomListener);


        // Define the drag listeners for drag/drop behaviour of nodes.
        dragListener = d3.behavior.drag()
            .on("dragstart", function(d) {
                if (d == root) {
                    return;
                }
                dragStarted = true;
                //nodes = tree.nodes(d);
                d3.event.sourceEvent.stopPropagation();
                // it's important that we suppress the mouseover event on the node being dragged. Otherwise it will absorb the mouseover event and the underlying node will not detect it d3.select(this).attr('pointer-events', 'none');
            })
            .on("drag", function(d) {
                if (d == root) {
                    return;
                }
                if (dragStarted) {
                    domNode = this;
                }

                // get coords of mouseEvent relative to svg container to allow for panning
                relCoords = d3.mouse($('svg').get(0));
                if (relCoords[0] < panBoundary) {
                    panTimer = true;
                    pan(this, 'left');
                } else if (relCoords[0] > ($('svg').width() - panBoundary)) {
                    panTimer = true;
                    pan(this, 'right');
                } else if (relCoords[1] < panBoundary) {
                    panTimer = true;
                    pan(this, 'up');
                } else if (relCoords[1] > ($('svg').height() - panBoundary)) {
                    panTimer = true;
                    pan(this, 'down');
                } else {
                    try {
                        clearTimeout(panTimer);
                    } catch (e) {

                    }
                }
            }).on("dragend", function(d) {
                if (d == root) {
                    return;
                }
                domNode = this;
                if (selectedNode) {
                    // now remove the element from the parent, and insert it into the new elements children
                    var index = draggingNode.parent.children.indexOf(draggingNode);
                    if (index > -1) {
                        draggingNode.parent.children.splice(index, 1);
                    }
                    if (typeof selectedNode.children !== 'undefined' || typeof selectedNode._children !== 'undefined') {
                        if (typeof selectedNode.children !== 'undefined') {
                            selectedNode.children.push(draggingNode);
                        } else {
                            selectedNode._children.push(draggingNode);
                        }
                    } else {
                        selectedNode.children = [];
                        selectedNode.children.push(draggingNode);
                    }
                    // Make sure that the node being added to is expanded so user can see added node is correctly moved
                    expand(selectedNode);
                    endDrag();
                } else {
                    endDrag();
                }
            });

        function endDrag() {
            selectedNode = null;
            d3.selectAll('.ghostCircle').attr('class', 'ghostCircle');
            d3.select(domNode).attr('class', 'node');
            // now restore the mouseover event or we won't be able to drag a 2nd time
            d3.select(domNode).select('.ghostCircle').attr('pointer-events', '');
            updateTempConnector();
            if (draggingNode !== null) {
                update(root);
                centerNode(draggingNode);
                draggingNode = null;
            }
        }

        var overCircle = function(d) {
            selectedNode = d;
            updateTempConnector();
        };
        var outCircle = function(d) {
            selectedNode = null;
            updateTempConnector();
        };

        // Function to update the temporary connector indicating dragging affiliation
        var updateTempConnector = function() {
            var data = [];
            if (draggingNode !== null && selectedNode !== null) {
                // have to flip the source coordinates since we did this for the existing connectors on the original tree
                data = [{
                    source: {
                        x: selectedNode.y0,
                        y: selectedNode.x0
                    },
                    target: {
                        x: draggingNode.y0,
                        y: draggingNode.x0
                    }
                }];
            }
            var link = svgGroup.selectAll(".templink").data(data);

            link.enter().append("path")
                .attr("class", "templink")
                .attr("d", d3.svg.diagonal())
                .attr('pointer-events', 'none');

            link.attr("d", d3.svg.diagonal());

            link.exit().remove();
        };

        // Function to center node when clicked/dropped so node doesn't get lost when collapsing/moving with large amount of children.

        function centerNode(source) {
            scale = zoomListener.scale();
            x = -source.y0;
            y = -source.x0;
            x = (x + 10 + root.leftdistance) * scale ;
            y = y * scale + 10;
            d3.select('g')
              .attr("transform", "translate(" + x + "," + y + ")scale(" + scale + ")");
            zoomListener.scale(scale);
            zoomListener.translate([x, y]);
        }

        function update(source) {
            // Compute the new height, function counts total children of root node and sets tree height accordingly.
            // This prevents the layout looking squashed when new nodes are made visible or looking sparse when nodes are removed
            // This makes the layout more consistent.
            var levelWidth = [1];
            var childCount = function(level, n) {

                if (n.children && n.children.length > 0) {
                    if (levelWidth.length <= level + 1) levelWidth.push(0);

                    levelWidth[level + 1] += n.children.length;
                    n.children.forEach(function(d) {
                        childCount(level + 1, d);
                    });
                }
            };
            childCount(0, root);
            var newHeight = d3.max(levelWidth) * 25; // 25 pixels per line
            tree = tree.size([newHeight, viewerWidth]);

            // Compute the new tree layout.
            var nodes = tree.nodes(root).reverse(),
                links = tree.links(nodes);

            // Set widths between levels based on maxLabelLength.
            var k = 0;
            var movenode = function(node, x, y) {
                node.x += x;
                node.y += y;
                if (node.hasOwnProperty('children')) {
                    for(var i = 0; i < node.children.length; i++) {
                        movenode(node.children[i], x, y);
                    }
                }
            };
            var computepos = function(node) {
                var i = 0, totalwidth, totalheight, middleindex;
                node.x = node.depth * 30;
                node.y = 0;
                node.width = (node.name.length + 1) * 8;
                node.height = 30;
                node.leftdistance = node.width / 2;
                node.rightdistance = node.width / 2;

                if (node.hasOwnProperty('children') == false) {
                    return;
                }

                if (node.children.length == 0) {
                    return;
                }

                totalwidth = 0;
                totalheight = 0;
                for(i = 0; i < node.children.length; i++) {
                    computepos(node.children[i]);
                    totalwidth += node.children[i].width;
                    totalheight = Math.max(node.children[i].height, totalheight);
                }
                node.width = Math.max(node.width, totalwidth);
                node.height = totalheight;
                var ypos = -totalwidth / 2;
                if (node.children.length % 2 == 0) {
                    middleindex = parseInt(Math.floor(node.children.length / 2));
                    ypos = 0;
                    for(i = middleindex - 1; i>= 0; i--) {
                        ypos -= node.children[i].rightdistance;
                        movenode(node.children[i], 0, ypos);
                        ypos -= node.children[i].leftdistance;
                    }
                    node.leftdistance = ypos * -1;
                    ypos = 0;
                    for(i = middleindex; i < node.children.length; i++) {
                        ypos += node.children[i].leftdistance;
                        movenode(node.children[i], 0, ypos);
                        ypos += node.children[i].rightdistance;
                    }
                    node.rightdistance = ypos;
                } else {
                   if (node.children.length == 1) {
                       node.leftdistance = Math.max(node.leftdistance, node.children[0].leftdistance);
                       node.rightdistance = Math.max(node.rightdistance, node.children[0].rightdistance);
                   } else {
                       middleindex =  parseInt(Math.floor(node.children.length / 2));
                       var pos = node.children[middleindex].leftdistance * - 1;
                       for(i = middleindex-1; i >= 0; i--) {
                          pos -= node.children[i].rightdistance;
                          movenode(node.children[i], 0, pos);
                          pos -= node.children[i].leftdistance;
                       }
                       node.leftdistance = pos * -1;
                       pos = node.children[middleindex].rightdistance;
                       for(i = middleindex+1; i < node.children.length; i++) {
                           pos += node.children[i].leftdistance;
                           movenode(node.children[i], 0, pos);
                           pos += node.children[i].rightdistance;
                       }
                       node.rightdistance = pos;
                   }
                }
            };
            nodes.forEach(function(d) {
                if (d.name == '(root)') {
                    computepos(d);
                }
            });

            // Update the nodes…
            node = svgGroup.selectAll("g.node")
                .data(nodes, function(d) {
                    return d.id || (d.id = ++i);
                });

            // Enter any new nodes at the parent's previous position.
            var nodeEnter = node.enter().append("g")
                .call(dragListener)
                .attr("class", "node")
                .attr("transform", function(d) {
                    return "translate(" + source.y0 + "," + source.x0 + ")";
                });

            nodeEnter.append("circle")
                .attr('class', 'nodeCircle')
                .attr("r", 0)
                .style("fill", function(d) {
                    return d._children ? "lightsteelblue" : "#fff";
                });

            nodeEnter.append("text")
                .attr("x", function(d) {
                    return 0;
                    //return d.children || d._children ? -10 : 10;
                })
                .attr("dy", "15")
                .attr('class', 'nodeText')
                .attr("text-anchor", function(d) {
                    return "middle";
                    //return d.children || d._children ? "end" : "start";
                })
                .text(function(d) {
                    return d.name;
                })
                .style("fill-opacity", 0);

            // phantom node to give us mouseover in a radius around it
            nodeEnter.append("circle")
                .attr('class', 'ghostCircle')
                .attr("r", 30)
                .attr("opacity", 0.2) // change this to zero to hide the target area
                .style("fill", "red")
                .attr('pointer-events', 'mouseover')
                .on("mouseover", function(node) {
                    overCircle(node);
                })
                .on("mouseout", function(node) {
                    outCircle(node);
                });

            // Update the text to reflect whether node has children or not.
            node.select('text')
                .attr("x", function(d) {
                    return 0;
                    //return d.children || d._children ? -10 : 10;
                })
                .attr("text-anchor", function(d) {
                    return "middle";
                    //return d.children || d._children ? "end" : "start";
                })
                .text(function(d) {
                    return d.name;
                });

            // Change the circle fill depending on whether it has children and is collapsed
            node.select("circle.nodeCircle")
                .attr("r", 4.5)
                .style("fill", function(d) {
                    return d._children ? "lightsteelblue" : "#fff";
                });

            // Transition nodes to their new position.

            var nodeUpdate = node.attr("transform", function(d) {
                    return "translate(" + d.y + "," + d.x + ")";
                });

            // Fade the text in
            nodeUpdate.select("text")
                      .style("fill-opacity", 1);

            // Transition exiting nodes to the parent's new position.
            var nodeExit = node.exit().attr("transform", function(d) {
                    return "translate(" + source.y + "," + source.x + ")";
                })
                .remove();

            nodeExit.select("circle")
                .attr("r", 0);

            nodeExit.select("text")
                .style("fill-opacity", 0);

            // Update the links…
            var link = svgGroup.selectAll("path.link")
                .data(links, function(d) {
                    return d.target.id;
                });

            // Enter any new links at the parent's previous position.
            link.enter().insert("path", "g")
                .attr("class", "link")
                .attr("d", function(d) {
                    var o = {
                        x: source.x0,
                        y: source.y0
                    };
                    return diagonal({
                        source: o,
                        target: o
                    });
                });

            // Transition links to their new position.
            link.attr("d", diagonal);

            // Transition exiting nodes to the parent's new position.
            link.exit().attr("d", function(d) {
                    var o = {
                        x: source.x,
                        y: source.y
                    };
                    return diagonal({
                        source: o,
                        target: o
                    });
                })
                .remove();

            // Stash the old positions for transition.
            nodes.forEach(function(d) {
                d.x0 = d.x;
                d.y0 = d.y;
            });
        }

        // Append a group which holds all nodes and which the zoom Listener can act upon.
        var svgGroup = baseSvg.append("g");

        // Define the root
        root = treeData;
        root.x0 = viewerHeight / 2;
        root.y0 = 0;

        // Layout the tree initially and center on the root node.
        update(root);
        centerNode(root);
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
                    var parent = {
                        "name" : "(root)",
                        "children" : []
                    };
                    var links = {}, parentnode;
                    for(var i = 0; i < data.length; i++) {
                        links[data[i].key] = data[i];
                        if (data[i].hasOwnProperty("parent")) {
                            parentnode = data[i]["parent"];
                            if (links[parentnode].hasOwnProperty("children") == false) {
                                links[parentnode].children = [];
                            }
                            links[parentnode].children.push(data[i]);
                        } else {
                            parent.children.push(data[i]);
                        }
                    }
                    $("#myDiagram").remove();
                    $("#sample").html("<div id=\"myDiagram\"></div>");
                    init_tree(parent);
                }
            });
        })
    })
</script>
<?
echo $OUTPUT->footer();