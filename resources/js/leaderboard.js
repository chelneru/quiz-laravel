$(document).ready(function () {
   console.log('page loaded');
   $('select').formSelect();

   LoadCharts();
   $('.class-filter').on('change',function () {
      filter_quizzes('class',$(this).find('option:selected').val()
      );
   });
});

function LoadCharts() {
   // Load the Visualization API and the corechart package.
   google.charts.load('current', {packages: ['corechart', 'line']});

   // Set a callback to run when the Google Visualization API is loaded.

   google.setOnLoadCallback(function () {
      DrawTimesChart('times_chart', $('#times_chart').attr('data-chart'));
      DrawScoresChart('scores_chart', $('#scores_chart').attr('data-chart'));

   });


}
function filter_quizzes(filter_name,filter_id) {
   window.location = '/leaderboards/'+filter_id;
}
function DrawTimesChart(identifier, info_data) {
   let user_value = $('#' + identifier).attr('data-user-value');
   info_data = JSON.parse(info_data);
   for (let iter = 0; iter < info_data.length; iter++) {
      if (user_value == parseFloat(info_data[iter])) {
         info_data[iter] = [iter + 1, parseFloat(info_data[iter]), 'Your time', user_value+'s'];

      } else {
         info_data[iter] = [iter + 1, parseFloat(info_data[iter]), null, null];
      }
   }
   // Create the data table.
   let data = new google.visualization.DataTable();


   data.addColumn('number', 'X');
   data.addColumn('number', 'Times');
   data.addColumn({type: 'string', role: 'annotation'});
   data.addColumn({type: 'string', role: 'annotationText'});

   data.addRows(info_data);

   let title = 'chart';
   // Set chart options
   let options = {

      0: {
         // set any applicable options on the first series
      },
      1: {
         // set the options on the second series
         lineWidth: 0,
         pointSize: 5,
         visibleInLegend: false
      },
      legend: 'none'
,
      colors:['#26a69a'],

      hAxis: {
         title: '',
         textColor: '#ffffff'
      },
      vAxis: {
         title: 'Time'
      }
   };
   _progress_initial_chart = new google.visualization.LineChart(document.getElementById(identifier));
   _progress_initial_chart.draw(data, options);

}

function DrawScoresChart(identifier, info_data) {
   let user_value = $('#' + identifier).attr('data-user-value');
   info_data = JSON.parse(info_data);


      info_data = Object.keys(info_data).map(function(key) {
         return  parseFloat(info_data[key]);
      });
   console.log(info_data);

   info_data = info_data.sort(function(a, b){return a - b});
      console.log(info_data);


   for (let iter = 0; iter < info_data.length; iter++) {
      if (user_value == parseFloat(info_data[iter])) {
         info_data[iter] = [ parseFloat(info_data[iter]), iter + 1,'Your score',user_value];

      } else {
         info_data[iter] = [ parseFloat(info_data[iter]),iter + 1, null, null];
      }
      console.log(info_data[iter]);
   }
   // Create the data table.
   let data = new google.visualization.DataTable();

   data.addColumn('number', 'Scores');

   data.addColumn('number', 'X');

   data.addColumn({type: 'string', role: 'annotation'});
   data.addColumn({type: 'string', role: 'annotationText'});

   data.addRows(info_data);

   let title = 'chart';
   // Set chart options
   let options = {
      0: {
         // set any applicable options on the first series
      },
      1: {
         // set the options on the second series
         lineWidth: 0,
         pointSize: 5,
         visibleInLegend: false
      },
      colors:['#26a69a'],
      orientation: 'vertical'
,
      legend: 'none'
,
      hAxis: {
         title: '',
         textColor: '#ffffff',
      },
      vAxis: {
         title: 'Scores'
      }
   };
   _progress_initial_chart = new google.visualization.LineChart(document.getElementById(identifier));
   _progress_initial_chart.draw(data, options);

}