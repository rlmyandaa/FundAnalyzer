<html>

<!-- Test Symbol Select API from RapidAPI !-->
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <div>
        <select class="js-data-example-ajax" style="width: 200px;"></select>
    </div>
    <script>
        $('.js-data-example-ajax').select2({
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
                    console.log(data.quotes);
                    return {
                        results: data.quotes
                    };
                },
                cache: true
            },
            placeholder: 'Search for Ticker / Symbol',
            minimumInputLength: 1,
            templateResult: formatSymbol,
            templateSelection: formatSymbolSelection
        });

        function formatSymbol(quote) {
            console.log("format repo");
            console.log(quote.symbol);
            return quote.symbol;
        }

        function formatSymbolSelection(sQuote) {
            console.log("format repo selection");
            console.log(sQuote.symbol);
            return sQuote.symbol;
        }
    </script>
</body>

</html>