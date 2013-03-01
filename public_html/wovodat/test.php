<! DOCTYPE html>
<html>
    <head>
        <title>
            Testing
        </title>
        <script type="text/javascript" src="/js/jqueryui/js/jquery-1.6.4.min.js"></script>
        <script type="text/javascript" src="/js/jqueryui/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.tuan.js"></script>
        <script type="text/javascript" src="/js/flot/jquery.flot.navigate.tuan.js"></script> 
        <script type="text/javascript" src="/js/flot/jquery.flot.selection.js"></script>
        <script type="text/javascript">
            $(function(){
                var data1 = [[[1,2],[5,3],[4,1]]];
                var placeholder = $("#placeholder");
                placeholder.hide();
                var options = {
                    grid:{
                        hoverable: true,
                        clickable: true
                    },
                    zoom:{
                        interactive: true
                    },
                    pan: {
                        interactive: true
                    }
                };
                    placeholder.show();
                var plot = $.plot(placeholder,data1,options);
                var data2 = [[[6,1],[2,3]]];
                window.setTimeout(function(){
                    v2();
                },1000);
                function v2(){
                    plot.setData(data2);
                    plot.draw();
                    window.setTimeout(function(){
                        v1();
                    },1000);
                }
                function v1(){
                    plot.setData(data1);
                    plot.draw();
                    window.setTimeout(function(){
                        v2();
                    },1000);
                }
            })
        </script>
        <style type="text/css">
            .placeholder{
                height: 300px;
                width: 500px;
                display: none;
            }
        </style>
    </head>
    
    <body>
        <div id="placeholder" class="placeholder">
            
        </div>
    </body>
</html>