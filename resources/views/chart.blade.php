<html>

<!-- Test Symbol Select API from RapidAPI !-->

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <div class="container">
        <h3>Search Symbol</h3>
        <select class="js-data-example-ajax" style="width: 25vw;">
        </select>
        <h4>Chart</h4>
        <div class="w-100" style="position: relative; height:40vh; width:40vw">
            <canvas id="myChart"></canvas>
        </div>
    </div>
    <script>
        $('.js-data-example-ajax').select2({
            placeholder: 'Search Ticker / Symbol',
            ajax: {
                async: true,
                crossDomain: true,
                // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                url: "https://apidojo-yahoo-finance-v1.p.rapidapi.com/auto-complete",
                method: "GET",
                headers: {
                    "x-rapidapi-key": "ef314b725dmshc94fd9b9b47de3dp1fd2adjsn150de4cfaa50",
                    "x-rapidapi-host": "apidojo-yahoo-finance-v1.p.rapidapi.com"
                },
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    //console.log(data);
                    data.quotes.forEach(function(entry) {
                        entry.id = '' + entry.symbol; // Better if you can assign a unique value for every entry, something like UUID
                    });
                    //console.log(data.quotes);
                    return {
                        results: data.quotes
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            templateResult: formatSymbol,
            templateSelection: formatSymbolSelection
        });

        function formatSymbol(quote) {
            //console.log("format repo");
            //console.log(quote.symbol);
            return (typeof quote.symbol !== 'undefined' ? (quote.symbol+ " | ") : "") + (typeof quote.shortname !== 'undefined' ? quote.shortname : "");
        }

        function formatSymbolSelection(sQuote) {
            //console.log("format repo selection");
            //console.log(sQuote.symbol);
            getChartData(sQuote.symbol);
            return sQuote.symbol;
        }
        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Empty Chart',
                    data: [],
                    borderColor: '#DDB967',
                    borderWidth: 1,
                    backgroundColor: '#DDB967',
                    fill: true,
                }]
            },
            options: {
                scales: {
                    y: [{
                        ticks: {
                            beginAtZero: true
                        },
                        min: 0
                    }]
                },
                elements: {
                    point: {
                        radius: 1
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        function changeData(chart, newLabel, newData, newTitle) {
            //console.log(newLabel);
            chart.data.labels = newLabel;
            chart.data.datasets[0].data = newData;
            chart.data.datasets[0].label = newTitle;
            //console.log(newData[0], newData[newData.length - 1])
            lastIndex = newData.length;

            while (lastIndex-- && !newData[lastIndex]);
            if (newData[0] > newData[lastIndex]) {
                chart.data.datasets[0].borderColor = "#D1603D";
                chart.data.datasets[0].backgroundColor = "#D1603D";
            } else if (newData[0] == newData[lastIndex]) {
                chart.data.datasets[0].borderColor = "#f2cc8f";
                chart.data.datasets[0].backgroundColor = "#f2cc8f";
            } else {
                chart.data.datasets[0].borderColor = "#81b29a";
                chart.data.datasets[0].backgroundColor = "#81b29a";
            }
            chart.update();
        }

        function getChartData(symbol) {
            //console.log("Chart Data");
            //console.log(symbol);
            $.ajax({
                async: true,
                crossDomain: true,
                url: "https://apidojo-yahoo-finance-v1.p.rapidapi.com/stock/v2/get-chart?interval=5m&symbol=" + symbol + "&range=1d&region=US",
                method: "GET",
                headers: {
                    "x-rapidapi-key": "ef314b725dmshc94fd9b9b47de3dp1fd2adjsn150de4cfaa50",
                    "x-rapidapi-host": "apidojo-yahoo-finance-v1.p.rapidapi.com"
                }
            }).done(function(response) {
                //console.log(response.chart.result[0]);
                var close = response.chart.result[0].indicators.quote[0].close;
                //console.log(close);
                var high = response.chart.result[0].indicators.quote[0].high;
                var low = response.chart.result[0].indicators.quote[0].low;
                var open = response.chart.result[0].indicators.quote[0].open;
                var volume = response.chart.result[0].indicators.quote[0].volume;
                var ts = response.chart.result[0].timestamp;
                var timestamp = [];
                ts.forEach(function(entry) {
                    timestamp.push(convertTimestamp(entry)); // Better if you can assign a unique value for every entry, something like UUID
                });
                //console.log(timestamp);
                changeData(myChart, timestamp, close, symbol);
            });
        }

        function convertTimestamp(ts) {
            var date = new Date(ts * 1000);
            // Hours part from the timestamp
            var hours = date.getHours();
            // Minutes part from the timestamp
            var minutes = "0" + date.getMinutes();
            // Seconds part from the timestamp
            var seconds = "0" + date.getSeconds();

            // Will display time in 10:30:23 format
            var formattedTime = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

            return formattedTime;
        }
    </script>
</body>

</html>