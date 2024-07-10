<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5bf9be4e76.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite('resources/css/app.css')
    <title>Back Office</title>
</head>
<body class="w-full h-screen overflow-hidden bg-[#f2f2f2]">
    <div class="w-full flex items-center h-[7%] bg-main px-10">
        <p class="text-lg font-medium text-white">Sales summary</p>
    </div>
    <div class="w-full h-[93%] flex">
        <div class="w-[5%] pt-10 bg-[#fefefe]">
            <div class="flex w-2/3 mx-auto flex-col items-center justify-center pb-4 mb-3">
                <img src="{{asset('images/logo-transparent.png')}}" alt="">
            </div>
            <div class="w-full relative">
                <button onclick="openDashboard()" class="w-full flex items-center justify-center h-auto py-4 bg-[#f5a7a4]">
                    <img src="{{asset('images/chart-red.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="dash_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 text-sm">
                    <div class="w-full flex flex-col py-2">
                        <a href="{{route('office.dashboard')}}" class="hover:bg-[#e6e6e6] p-2">Sales summary</a>
                        <a href="{{route('office.sales_by_item')}}" class="hover:bg-[#e6e6e6] p-2">Sales by item</a>
                        <a href="{{route('office.sales_history')}}" class="hover:bg-[#e6e6e6] p-2">Sales history</a>
                    </div>
                </div>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.items_list')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/prod-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <button onclick="openInventoryOptions()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/inv-new.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="inventory_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.stocks_adjustment')}}">Stocks Adjustment</a>
                        <a href="{{route('office.inventory')}}">Inventory</a>
                    </div>
                </div>
            </div>
            {{-- <div class="w-full relative">
                <a href="{{route('qr_printing')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/qr.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div> --}}
            <div class="w-full relative">
                <a href="{{route('office.cashiers')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/employee-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.supplier')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/supplier-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.ordering')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/order-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.logout')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/logout-new.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
        </div>
        <div id="main" class="w-[95%] p-7 h-screen overflow-auto">
            <div class="w-full">
                <div class="mb-10">
                    <div class="bg-white mb-7 p-6">
                        <select name="date" id="date" class="outline-none p-2 border">
                            <script>
                                var currentYear = new Date().getFullYear(); // Fetch the current year
                                var currentMonth = new Date().getMonth(); // Fetch the current month (0-indexed)
                                var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                                for (var i = 0; i < months.length; i++) {
                                    var option = document.createElement("option");
                                    option.text = months[i] + " " + currentYear; // Add current year to the month
                                    option.value = months[i] + " " + currentYear; // Add current year to the value
                                    if (i === currentMonth) {
                                        option.selected = true; // Automatically select the current month
                                    }
                                    document.getElementById("date").add(option);
                                }
                            </script>
                        </select>
                        <div class="w-full flex items-center justify-evenly">
                            <div class="w-1/3 flex flex-col items-center justify-center">
                                <p class="">Gross sales</p>
                                <span id="gross_sales" class="text-2xl font-medium">₱{{$gross_sales}}.00</span>
                            </div>
                            <div class="w-1/3 flex flex-col items-center justify-center">
                                <p>Net sales</p>
                                <span id="net_sales" class="text-2xl font-medium">₱{{$net_sales}}</span>
                            </div>
                        </div>
                        <div class="w-11/12">
                            <canvas id="myChart" class="w-full"></canvas>
                        </div>
                    </div>
                    <div class="w-full bg-white p-6">
                        {{-- <form action="{{route('export_sales')}}" method="POST" target="__blank">
                            @csrf
                            <button class="font-medium bg-gray-600 uppercase py-2 text-white w-[100px] mb-2 rounded-sm text-xs">Export</button>
                        </form> --}}
                        <div class="w-full flex items-center py-3 border-y justify-between font-semibold">
                            <p class="w-1/2 text-left">Date</p>
                            <p class="w-1/2 text-right">Gross sales</p>
                            <p class="w-1/2 text-right">Net sales</p>
                            <p class="w-1/2 text-right">Gross profit</p>
                        </div>
                        <div class="w-full">
                            @foreach ($dailyTotalSales as $date => $total)
                                <div class="py-3 flex w-full justify-between border-b">
                                    <p class="w-1/4">{{$date}}</p>
                                    <p class="w-1/4 text-right">₱{{$total}}.00</p>
                                    <p class="w-1/4 text-right">₱{{ isset($dailySubTotalSales[$date]) ? $dailySubTotalSales[$date] : 0 }}</p>
                                    <p class="w-1/4 text-right">
                                        @php
                                            $profit = $total - (isset($dailySubTotalSales[$date]) ? $dailySubTotalSales[$date] : 0);
                                            echo '₱'.$profit;
                                        @endphp
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            const ctx = document.getElementById('myChart');
            const dailySales = @json($sales);
        
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(dailySales), // Use the formatted dates as labels
                    datasets: [{
                        label: 'Total sales in Peso (₱)',
                        data: Object.values(dailySales), // Use the sales values
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        
    </div>
    <script>
        var main = document.getElementById('main')

        $(document).ready(function(){
            $("#date").change(function(){
                var selectedDate = $(this).val();

                // Make AJAX request
                $.ajax({
                    url: "/back-office/get_monthly_sales",
                    method: "GET",
                    data: { selectedDate: selectedDate },
                    success: function(response){

                        // Update gross sales and net sales values
                        $("#gross_sales").text('₱' + response.gross_sales.toFixed(2));
                        $("#net_sales").text('₱' + response.net_sales.toFixed(2));

                        // If chart exists, update chart data
                        var ctx = document.getElementById('myChart');
                        var chart = Chart.getChart(ctx);
                        if (chart) {
                            chart.data.labels = Object.keys(response.daily_sales);
                            chart.data.datasets[0].data = Object.values(response.daily_sales);
                            chart.update();
                        } else {
                            // Create new chart if it doesn't exist
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: Object.keys(response.daily_sales),
                                    datasets: [{
                                        label: 'Total sales in Peso (₱)',
                                        data: Object.values(response.daily_sales),
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error){
                        // Handle errors here
                        console.error(xhr.responseText);
                    }
                });
            });
        });

        function openInventoryOptions(){
            var inventoryOptions = document.getElementById('inventory_options')
            inventoryOptions.classList.toggle('hidden')
            main.classList.toggle('blur-5px')
        }

        function openDashboard(){
            var inventoryOptions = document.getElementById('dash_options')
            inventoryOptions.classList.toggle('hidden')
            main.classList.toggle('blur-5px')
        }

        function openItems(){
            var inventoryOptions = document.getElementById('items_options')
            inventoryOptions.classList.toggle('hidden')
            main.classList.toggle('blur-5px')
        }
    </script>
</body>
</html>