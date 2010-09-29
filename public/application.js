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
  
  $.getJSON("api.php?api=/election/1", function(data) {
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