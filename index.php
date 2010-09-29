<?
  
  // a simple way to get around the same origin policy that prevents cross-domain Javascript
  // AJAX requests: build a quick and dirty PHP method of getting them and rendering them.
  // to keep our Javascript less verbose, we'll only ever ask for stuff after the API address
  if ($_GET['api']) {
    echo file_get_contents("http://elections.raisethehammer.org/api" . $_GET['api']);
    exit;
  }
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html lang='en'> 
  <head> 
    <meta content='text/html; charset=utf-8' http-equiv='Content-Type' /> 
    <title>Gender of Candidates in Hamilton's 2010 Municipal Election</title> 
    <link charset='utf-8' href='public/style.css' media='screen' rel='stylesheet' title='no title' type='text/css' /> 
    <script src='public/jquery.js' type='text/javascript'></script>
    <script src='public/jquery.corner.js' type='text/javascript'></script> 
    <script src='public/jgcharts.pack.js' type='text/javascript'></script> 
  	
    <script type="text/javascript">
      function getGenderCount(candidates) {
        female_count = 0; male_count = 0; transgendered_count = 0;
        for (i = 0; i < candidates.length; i++) {
          switch (candidates[i].gender) {
            case "Female":
              female_count++;
              break;
            case "Male":
              male_count++;
              break;
            case "Transgendered":
              transgendered_count++;
              break;
          }
        } 
        return { females: female_count, males: male_count, trans: transgendered_count };
      }
      
      function renderChart(container) {
        api = new jGCharts.Api();
        $('<img>')
          .attr('src', api.make({
            data: [female_count, male_count],
            axis_labels: ['F (' + female_count + ')', 'M (' + male_count + ')'],
            type: 'p'
          })).appendTo(container);      
      }
      
    	$(function() {
        $('#content').corner("12px");        
        $(".fancy").fancybox();
        
        $.getJSON("?api=/election/1", function(data) {
          candidates = data.candidates;
          count = getGenderCount(candidates);
          
          $("#total_candidates_count").html(candidates.length);
          $("#female_count").html(count.females);
          $("#male_count").html(count.males);
          $("#transgendered_count").html(count.trans);
          
          renderChart("#all_candidates_chart");
          
          wards = data.wards;
          for (i = 0; i < wards.length; i++) {
            $("#wards").append("<h3>" + wards[i].ward + "</h3>");
          }
        });
    	});
    </script> 
  </head> 
  <body> 
    <div id='container'> 
      <div id='header'><h1>Gender of Candidates in Hamilton's 2010 Municipal Election</h1></div>
      <div id='content'>
        
        <h2>All Candidates</h2>
        
        <table>
          <tr><th>Total Candidates</th><td id="total_candidates_count"></td></tr>
          <tr><th>Female</th><td id="female_count"></td></tr>
          <tr><th>Male</th><td id="male_count"></td></tr>
          <tr><th>Transgendered</th><td id="transgendered_count"></td></tr>
        </table>
        
        <div id="all_candidates_chart"></div>
        
        <div id="wards"></div>
        
      </div>
      <div id='footer'> 
        <p>
          
        </p> 
      </div>
    </div>
  </body> 
</html>