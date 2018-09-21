<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css"
          rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">


    <script type="text/javascript" src="https://d3js.org/d3.v3.min.js"></script>


    <style type="text/css">
        .node {
            stroke: #000;
        / / stroke-width: 1.5 px;
        }

        .link {
            stroke: #000;
            stroke-opacity: .6;
        }

        .node text {
            pointer-events: none;
        / / font: 12 px sans-serif;
            stroke-width: 0;
        }
    </style>

    <title>Systemic Action Map</title>


    <script type='text/javascript'>//<![CDATA[
        <?php

        /*
        <script type='text/javascript'>//<![CDATA[
        array(3) {
          ["token"]=>
          string(165) "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ1c2VybmFtZSIsInN1YiI6IjM3IiwiaWF0IjoxNTA0ODgwMjQ2LCJleHAiOjE1MDU0ODUwNDZ9.dcfsIpLa9tL-SRch2whQQyYgwXQQpuGOB-XxL_IbZsk"
          ["id"]=>
          string(2) "42"
          ["t"]=>
          string(13) "1504880398516"
        }

        */

        $curl = curl_init();

        //        $url = 'http://144.76.5.203/olsetapp/systemicmap/getItem/';//dev
        $url = 'http://olback.gr/systemicmap/getItem/';//local

        curl_setopt_array($curl, [
            CURLOPT_URL => $url . $_REQUEST['id'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer ' . $_REQUEST['token']
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:' . $err;
        } else {
            //
        }

        ?>


        window.onload = function () {
            var json = {};
            json = <?php
            echo json_encode(json_decode($response)->data->data);
            ?> ;

            // json = {
            //     "nodes": [
            //         {
            //             "id": "1",
            //             "systemic_map_id": "1",
            //             "name": "QT 1 ?",
            //             "proposal": "QT 1 Answ.",
            //             "group": 10000,
            //             "groupColor": "#ffffff"
            //         }, {
            //             "id": "2",
            //             "systemic_map_id": "1",
            //             "name": "QT 2?",
            //             "proposal": "QT Answ.",
            //             "group": 2,
            //             "groupColor": "#c0e5e0"
            //         }, {
            //             "id": "3",
            //             "systemic_map_id": "1",
            //             "name": "QT1 3?",
            //             "proposal": "QT Answ 3.",
            //             "group": 4,
            //             "groupColor": "#dec0e5"
            //         }, {
            //             "id": "4",
            //             "systemic_map_id": "1",
            //             "name": "QT1 3?",
            //             "proposal": "QT Answ 4.",
            //             "group": 5,
            //             "groupColor": "#a3b"
            //         }
            //         , {
            //             "id": "5",
            //             "systemic_map_id": "1",
            //             "name": "QT1 5?",
            //             "proposal": "QT Answ 5.",
            //             "group": 4,
            //             "groupColor": "#00ff00"
            //         }, {
            //             "id": "6",
            //             "systemic_map_id": "1",
            //             "name": "QT1 6?",
            //             "proposal": "QT Answ 6.",
            //             "group": 5,
            //             "groupColor": "#0000ff"
            //         }
            //     ],
            //     "links": [
            //         {"source": 0, "target": 0, "value": 2},
            //         {"source": 0, "target": 1, "value": 2},
            //         {"source": 1, "target": 2, "value": 2},
            //
            //         {"source": 0, "target": 3, "value": 3},
            //         {"source": 0, "target": 4, "value": 2},
            //         {"source": 1, "target": 4, "value": 3},
            //         {"source": 2, "target": 5, "value": 3}
            //     ]
            // };


            var width = document.body.clientWidth,
                height = width,
                circleRadius = width * 0.055,
                fontRatio = 0.16,
                lineSizeParam = 10;
            var seed = 1;

            function random() {
                var x = Math.sin(seed++) * 10000;
                return x - Math.floor(x);

            }

            var i;
            for (i = 0; i < json.nodes.length; i++) {
                if (i === 0) {
                    json.nodes[i].x = width / 2;
                    json.nodes[i].y = height / 2;
                    json.nodes[i].fixed = true
                } else {
                    json.nodes[i].x = width / 2 + random() * circleRadius * json.nodes[i].group;
                    json.nodes[i].y = height / 2 + random() * circleRadius * json.nodes[i].group;
                }
            }
            var k = Math.sqrt(json.nodes.length / (width * height));
            var color = d3.scale.category20();
            var force = d3.layout.force()
            //.friction(0.9)
                .charge(-8 / k)
                .gravity(11 * k)
                .linkDistance(circleRadius * 1.60)
                .size([width, height]);

            var svg = d3.select("body").append("svg")
                .attr("width", width)
                .attr("height", height)
                .attr("id", "graph");

            if (json.nodes.length > 1) {
                force.nodes(json.nodes)
                    .links(json.links)
                    .start();
            } else {
                force.nodes(json.nodes)
                // .links(json.links)
                    .start();
            }

            for (i = 0; i < 25; i++) {
                force.tick();
            }


            var link = svg.selectAll(".link")
                .data(json.links)
                .enter().append("line")
                .attr("class", function (d) {
                    return ["link", d.source.name, d.target.name].join(" ");
                })
                .style("stroke-width", function (d) {
                    return Math.sqrt(d.value);
                });

// Set up dictionary of neighbors
            var node2neighbors = {};
            for (i = 0; i < json.nodes.length; i++) {
                var name = json.nodes[i].question;
                node2neighbors[name] = json.links.filter(function (d) {
                    return d.source.name === name || d.target.name === name;
                }).map(function (d) {
                    return d.source.name === name ? d.target.name : d.source.name;
                });
            }

            var clickableNodes = ["Node1"];

            var nodes = svg.selectAll(".node")
                .data(json.nodes)
                .enter().append("g")
                .attr("class", "node")
                .on("mouseover", mouseover)
                .on("mouseout", mouseout)
                .call(force.drag);

            function mouseover(d) {
                d3.select(this).select("circle").transition()
                    .duration(100)
                    .style("stroke-width", "2px")
                    .attr("r", circleRadius * 1.1);
            }


            function mouseout(d) {
                d3.select(this).select("circle").transition()
                    .duration(100)
                    .style("stroke-width", function (d) {
                        var sw = "1.0px";
                        if (d.index === 0) sw = "1.4px";
                        return sw;
                    })
                    .attr("r", circleRadius);
            }

            nodes.append("circle")
                .attr("id", function (n) {
                    return n.name;
                })
                .attr("r", circleRadius)
                .attr("x", function (n) {
                    return n.x
                })
                .attr("y", function (n) {
                    return n.y
                })
                .style("stroke-width", function (d) {
                    var stw = "1.4px";
                    if (d.group === 1) {
                        stw = "2px";
                    }
                    return stw;
                })
                .style("fill", function (d) {
                    var col = d.groupColor;//"#9bdaff";
                    //if (d.group === 1) col = "#ffd59b";
                    //if (d.group === 2) col = "#ff9bd5";
                    return col;
                });


            nodes.append("text")
            //.attr("dx", -circleRadius)
                .style("font-family", "sans-serif")
                .style("font-size", (fontRatio * circleRadius) + "px")
                .style("font-weight", function (d) {
                    var fw = "normal";
                    if (d.group === 1) {
                        fw = "bold";
                    }
                    return fw;
                })
                .attr("dy", 0)
                .text(function (d) {
                    return d.name
                }).call(wrap, circleRadius + lineSizeParam);
            nodes.select("text").each(function (d) {
                //var text=d3.select("text");
                //text[0][0].attr("y",50);
            });

            nodes.filter(function (n) {
                return clickableNodes.indexOf(n.name) !== -1;
            })

                .on("click", function (n) {
                    /*
                        // Determine if current node's neighbors and their links are visible
                        var active   = n.active ? false : true // toggle whether node is active
                        , newOpacity = active ? 0 : 1;

                        // Extract node's name and the names of its neighbors
                        var name     = n.name
                        , neighbors  = node2neighbors[name];

                        // Hide the neighbors and their links
                        for (var i = 0; i < neighbors.length; i++){
                            d3.select("circle#" + neighbors[i]).style("opacity", newOpacity);
                            d3.selectAll("line." + neighbors[i]).style("opacity", newOpacity);
                        }
                        // Update whether or not the node is active
                        n.active = active;
                        */
                });

//nodes.append("title").text(function(d) { return d.name; });

            force.on("tick", function () {
                link.attr("x1", function (d) {
                    return d.source.x;
                })
                    .attr("y1", function (d) {
                        return d.source.y;
                    })
                    .attr("x2", function (d) {
                        return d.target.x;
                    })
                    .attr("y2", function (d) {
                        return d.target.y;
                    });
                /*
                  nodes.attr("cx", function(d) { return d.x; })
                  .attr("cy", function(d) { return d.y; });*/
                nodes.attr("transform", function (d) {
                    return "translate(" + d.x + "," + d.y + ")";
                });
            });


            function wrap(text, width) {
                text.each(function () {
                    var text = d3.select(this),
                        words = splitIntoLines(text.text(), 20).reverse(), //text.text().split(/\s+/).reverse(),
                        word,
                        line = [],
                        lineNumber = 0,
                        lineHeight = 1.0, // ems
                        y = text.attr("y") - (circleRadius / 2),
                        dy = parseFloat(text.attr("dy")),
                        tspan = text.text(null).append("tspan")
                            .attr("x", 0)
                            .attr("y", y)
                            .attr("dy", dy + "em")
                            .attr("text-anchor", "middle");
                    var lineNum = 0;
                    while (word = words.pop()) {
                        line.push(word);
                        tspan.text(line.join(" "));
                        if (tspan.node().getComputedTextLength() > width) {
                            line.pop();
                            tspan.text(line.join(" "));
                            line = [word];
                            lineNum++;
                            if (lineNum < 7) {
                                tspan = text.append("tspan")
                                    .attr("x", 0)
                                    .attr("y", y)
                                    .attr("dy", ++lineNumber * lineHeight + dy + "em")
                                    .attr("text-anchor", "middle")
                                    .text(word);
                            } else {
                                tspan = text.append("tspan")
                                    .attr("x", 0)
                                    .attr("y", y)
                                    .attr("dy", ++lineNumber * lineHeight + dy + "em")
                                    .attr("text-anchor", "middle")
                                    .text(word + "...");
                                break;
                            }
                        }


                    }

                });
                //text.each(function(){
                //var text=d3.select(this);
                //text.attr("y",150);
                //var th=text[0][0].getBBox().height;
                //    text[0][0].y=50+(circleRadius-th)/2;

                //});
            }

            function splitIntoLines(input, len) {
                var i;
                var output = [];
                var lineSoFar = "";
                var temp;
                var words = input.split(' ');
                for (i = 0; i < words.length;) {
                    // check if adding this word would exceed the len
                    temp = addWordOntoLine(lineSoFar, words[i]);
                    if (temp.length > len) {
                        if (lineSoFar.length === 0) {
                            lineSoFar = temp; // force to put at least one word in each line
                            i++; // skip past this word now
                        }
                        output.push(lineSoFar); // put line into output
                        lineSoFar = ""; // init back to empty
                    } else {
                        lineSoFar = temp; // take the new word
                        i++; // skip past this word now
                    }
                }
                if (lineSoFar.length > 0) {
                    output.push(lineSoFar);
                }
                return (output);
            }

            function addWordOntoLine(line, word) {
                if (line.length !== 0) {
                    line += " ";
                }
                return (line += word);
            }


        }


        //]]>

    </script>


</head>

<body>
<div style="position:absolute;z-index:2000;">
    <?php
    $t = time();
    if ($_REQUEST['v'] == '1') {
        echo '<a class="btn" onClick="location.reload();" href="#"><i class="icon-repeat"></i> Reload</a>';
        echo '<a class="btn" onClick="window.print();" href="#"><i class="icon-print"></i> </a>';
    }
    echo '<a class="btn" onClick="scaleUp();" href="#"><i class="icon-zoom-in"></i> </a>';
    echo '<a class="btn" onClick="scaleDown();" href="#"><i class="icon-zoom-out"></i> </a>';
    //echo($t . "<br>");
    ?>
</div>

<script type='text/javascript'>//<![CDATA[
    var scale = 1.0;

    function scaleUp() {
        scale = scale * 1.1;
        document.getElementById("graph").setAttribute("transform", "scale(" + scale + ")");
    }

    function scaleDown() {
        scale = scale * 0.9;
        document.getElementById("graph").setAttribute("transform", "scale(" + scale + ")");
    }

    //]]> </script>
</body>

</html>

