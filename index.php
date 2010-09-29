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
        var female_count = 0; var male_count = 0; var transgendered_count = 0;
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
      
      function getCandidatesInWard(candidates, ward) {
        var candidates_in_ward = [];
        for (i = 0; i < candidates.length; i++) {
          if (candidates[i].ward == ward) {
            candidates_in_ward.push(candidates[i]);
          }
        }
        return candidates_in_ward;
      }
      
      function createChart(female_count, male_count) {
        var api = new jGCharts.Api();
        return  $('<img>')
                .attr('src', api.make({
                  data: [female_count, male_count],
                  axis_labels: ['F (' + female_count + ')', 'M (' + male_count + ')'],
                  type: 'p'
                }));
      }
      
    	$(function() {
        $('#content').corner("12px");        
        
        $.getJSON("?api=/election/1", function(data) {
          var candidates = data.candidates;
          var count = getGenderCount(candidates);
          
          $("#total_candidates_count").html(candidates.length);
          $("#female_count").html(count.females);
          $("#male_count").html(count.males);
          $("#transgendered_count").html(count.trans);
          
          $("#all_candidates_chart").append(createChart(count.females, count.males));
          
          var wards = data.wards;
          for (ward_index = 0; ward_index < wards.length; ward_index++) {
            // figure out who the candidates are in the ward and build the counts for them
            var candidates_in_ward = getCandidatesInWard(candidates, wards[ward_index].ward);
            var ward_count = getGenderCount(candidates_in_ward);
            // render a div with the ward title and chart in it for each of the wards
            $("#wards").append(
              $("<div class='ward'></div>")
                .append("<h3>" + wards[ward_index].ward + "</h3>")
                .append(createChart(ward_count.females, ward_count.males))
            );
          }
          $("#wards").append($("<div class='spacer'></div>"));
        });
    	});
    </script> 
  </head> 
  <body> 
    <div id='container'> 
      <div id='header'><h1>Gender of Candidates in Hamilton's 2010 Municipal Election</h1></div>
      <div id='content'>
        
        <p>
          This project utilizes the excellent resources and API of the <a href="http://elections.raisethehammer.org/">Raise the Hammer Elections website</a>.
          The source is open and available on <a href="http://github.com/adriand/Female-and-Male-Candidates">GitHub</a>.
        </p>
        <br /><br />
        
        <h2>All Candidates</h2>
        
        <div id="all_candidates_chart" style="float: left;"></div>
                
        <div style="float: left; padding-top: 50px; margin-left: 30px;">
          <table>
            <tr><th>Total Candidates</th><td id="total_candidates_count"></td></tr>
            <tr><th>Female</th><td id="female_count"></td></tr>
            <tr><th>Male</th><td id="male_count"></td></tr>
            <tr><th>Transgendered</th><td id="transgendered_count"></td></tr>
          </table>
        </div>
        
        <div class="spacer"></div>
        
        <br /><br />
        <h2>By Wards</h2>
        
        <div id="wards"></div>
        
      </div>
      <div id='footer'> 
        <p>
          Copyright &copy; 2010 Raise the Hammer. <a href="http://github.com/adriand/Female-and-Male-Candidates">Get the source (it's open!)</a>
        </p> 
      </div>
    </div>
  </body> 
</html>