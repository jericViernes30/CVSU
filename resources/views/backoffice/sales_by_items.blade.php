<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5bf9be4e76.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @vite('resources/css/app.css')
    <title>Back Office</title>
</head>
<body class="w-full h-screen overflow-hidden bg-[#f2f2f2]">
    <div class="w-full flex items-center h-[7%] bg-[#db121c] px-10">
        <p class="text-lg font-medium text-white">Sales by item</p>
    </div>
    <div class="w-full h-[93%] flex ">
        <div class="w-[5%] pt-10 bg-[#fefefe]">
            <div class="w-full relative">
                <button onclick="openDashboard()" class="w-full flex items-center justify-center h-auto py-4 border-l-[3px] border-[#4caf50] bg-[#f2f2f2]">
                    <img src="{{asset('images/chart.png')}}" alt="" class="w-[30px] h-auto">
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
                    <img src="{{asset('images/products.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <button onclick="openInventoryOptions()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/inventory.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="inventory_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.stocks_adjustment')}}">Stocks Adjustment</a>
                        <a href="{{route('office.inventory')}}">Inventory</a>
                    </div>
                </div>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.cashiers')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="#5c6bc0" width="25" height="25"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/></svg>
                </a>
            </div>
            <div class="w-full relative">
                <form action="{{route('office.logout')}}" method="POST" class="w-full flex items-center justify-center h-auto py-4">
                    @csrf
                    <button>
                        <i class="fa-solid fa-arrow-right-from-bracket fa-xl"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="w-[95%] p-7 h-screen overflow-auto">
            <div class="w-full">
                <div class="mb-10">
                    <div class="w-full bg-white p-6 flex gap-2">
                        <div class="w-1/5 border-r-2 pr-2">
                            <p id="item" class="mb-5 font-semibold">Top 10 Items for the month of <span id="month">June</span></p>
                            <div class="w-full flex flex-col gap-2 items-start">
                              @foreach ($topItems as $topItem)
                                  <button id="items"  data-val="{{$topItem->food_name}}" class="w-full py-1 text-start">{{$topItem->food_name}}</button>
                              @endforeach
                            </div>
                        </div>
                        <div class="w-4/5">
                            <canvas id="myChart" class="w-full"></canvas>
                        </div>
                    </div>
                </div>
                <div class="w-full p-6 bg-white mb-10">
                    <div class="w-full flex items-center py-3 border-y text-sm text-gray-400 font-medium">
                        <p class="w-[40%]">Item</p>
                        <p class="w-[15%] text-right">Items sold</p>
                        <p class="w-[15%] text-right">Net sales</p>
                        <p class="w-[15%] text-right">Item cost</p>
                        <p class="w-[15%] text-right">Gross profit</p>
                    </div>
                    <div class="w-full">
                        @foreach ($items as $item)
                            <div class="w-full flex items-center py-3 border-b">
                                <p class="w-[40%]">{{ $item->food_name }}</p>
                                <p class="w-[15%] text-right">{{ $item->occurrence }}</p>
                                <p class="w-[15%] text-right">&#8369;{{ ($item->retail * $item->occurrence) - (($item->retail * $item->occurrence) * .12)}}</p>
                                <p class="w-[15%] text-right">&#8369;{{ $item->cost * $item->occurrence }}</p>
                                <p class="w-[15%] text-right">&#8369;
                                    {{
                                        (($item->retail * $item->occurrence) - (($item->retail * $item->occurrence) * .12)) - ($item->cost * $item->occurrence)
                                    }}
                                </p>
                            </div>
                        @endforeach
                        <div class="w-full flex items-center gap-10 pt-4 px-6 text-sm text-gray-500">
                            {{ $items->links() }}
                            <div class="flex items-center gap-1 w-[8%]">
                                <p>Page: {{ $items->currentPage() }}</p>
                                <p>of {{ $items->lastPage() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
          const ctx = document.getElementById('myChart').getContext('2d');
          
          // Extracting item names and occurrence counts from PHP and formatting them for JavaScript
          const itemNames = {!! json_encode($topItems->pluck('food_name')) !!};
          const itemOccurrences = {!! json_encode($topItems->pluck('occurrence')) !!};
      
          // Example sales data for the top items
          const datasets = [{
              label: 'Sold item for this month',
              data: itemOccurrences,
              backgroundColor: 'rgba(255, 99, 132, 0.5)',
              borderColor: 'rgba(255, 99, 132, 1)',
              borderWidth: 1
          }];
      
          new Chart(ctx, {
              type: 'bar',
              data: {
                  labels: itemNames,
                  datasets: datasets
              },
              options: {
                  scales: {
                      y: {
                          beginAtZero: true,
                          stacked: true
                      },
                      x: {
                          stacked: true
                      }
                  }
              }
          });
      </script>
        
    </div>
    <script>
      $(document).ready(function() {
          // Attach click event handler to buttons with data-val attribute
          $("button[data-val]").click(function() {
              // Get the value of the data-val attribute
              var buttonValue = $(this).data("val");
              // Do something with the buttonValue, for example, log it to the console
              var url = "{{ route('office.item_name', ['item_name' => ':item_name']) }}";
              url = url.replace(':item_name', buttonValue);
              alert(url); // This is just for testing purposes
              
              // AJAX request
              $.ajax({
    url: url,
    method: "GET",
    success: function(response) {
        $('#month').textContent = response.month;
        // Extract labels (dates) and data (counts) from the response
        var labels = Object.keys(response.summed_counts);
        var data = Object.values(response.summed_counts);

        // Get the canvas context
        var ctx = document.getElementById('myChart').getContext('2d');
        
        // Check if the chart exists
        var existingChart = Chart.getChart(ctx);
        if (existingChart) {
            // If the chart exists, destroy it
            existingChart.destroy();
        }

        // Create a new chart
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Item sold on this day', // Provide a label for the dataset
                    data: data,
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
    },
    error: function(xhr, status, error) {
        // Handle error response here
    }
});

          });
      });



        function openInventoryOptions(){
            var inventoryOptions = document.getElementById('inventory_options')
            inventoryOptions.classList.toggle('hidden')
        }

        function openDashboard(){
            var inventoryOptions = document.getElementById('dash_options')
            inventoryOptions.classList.toggle('hidden')
        }

        function openItems(){
            var inventoryOptions = document.getElementById('items_options')
            inventoryOptions.classList.toggle('hidden')
        }
    </script>
    </script>
</body>
</html>