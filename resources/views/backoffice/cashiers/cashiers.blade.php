<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/5bf9be4e76.js" crossorigin="anonymous"></script>
    @vite('resources/css/app.css')
    <title>Back Office</title>
</head>
<body class="w-full h-screen bg-[#fefefe]">
    <div id="add" class="w-1/4 hidden h-auto absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-slate-50 z-10 p-4 rounded-md shadow-lg">
        <div class="w-full flex justify-end">
            <button onclick="closeCashierModal()" class="">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="14" height="14"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
            </button>
        </div>
        <div class="w-full">
            <p class="text-center mb-2 font-medium text-lg">Add cashier</p>
            <form action="{{route('office.add_cashier')}}" method="POST" class="text-sm">
                @csrf
                <label for="" class="">Cashier name</label>
                <input type="text" name="cashier_name" class="w-full outline-none p-2 border-b-2 mb-2 mt-1 focus:bg-main">
                <label for="" class="">Password</label>
                <input type="text" name="password" class="w-full outline-none p-2 border-b-2 mt-1 mb-4 focus:bg-main">
                <button class="bg-main py-1 px-10 rounded-sm uppercase font-medium text-sm float-right">Save</button>
            </form>
        </div>
    </div>
    <div id="head" class="w-full flex items-center h-[7%] bg-[#db121c] px-10">
        <p class="text-lg text-white">Items List</p>
    </div>
    <div id="body" class="w-full h-[93%] flex z-0">
        <div class="w-[5%] pt-10">
            <div class="w-full relative">
                <button onclick="openDashboard()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/chart.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="dash_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.dashboard')}}">Sales Summary</a>
                        <a href="{{route('office.sales_by_item')}}">Sales by item</a>
                        <a href="{{route('office.sales_history')}}">Sales history</a>
                    </div>
                </div>
            </div>
            <div class="w-full relative">
                <button onclick="openItems()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/products.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="items_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.items_list')}}">Items List</a>
                    </div>
                </div>
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
                <a href="{{route('qr_printing')}}" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/qr.png')}}" alt="" class="w-[30px] h-auto">
                </a>
            </div>
            <div class="w-full relative">
                <a href="{{route('office.cashiers')}}" class="w-full flex items-center justify-center h-auto py-4 border-l-[3px] border-[#5c6bc0] bg-[#f2f2f2]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="#5c6bc0" width="34" height="34"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/></svg>
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
        <div id="main" class="w-[95%] bg-[#f2f2f2] z-0 p-7">
            <div class="w-1/3 bg-slate-50 shadow-md p-6">
                <div class="w-full pb-1 flex gap-4 items-center justify-start">
                    <button onclick="openAddCashier()" class="px-5 bg-main font-medium uppercase text-xs py-2">Add Cashier</button>
                    {{-- <button class="font-medium bg-gray-600 uppercase py-2 text-white w-[100px] mb-2 rounded-sm text-xs">Export</button> --}}
                </div>
                <div class="w-full flex items-center text-sm py-4 px-5 text-gray-500 border-b border-[#dadada]">
                    <p class="w-[50%]">Cashier name</p>
                    <p class="w-[50%]">Created at</p>
                </div>
                <div class="w-full">
                    @foreach ($cashiers as $cashier)
                        <div class="w-full flex items-center text-sm py-4 px-5 text-gray-700 border-b border-[#dadada]">
                            <p class="w-[50%]">{{$cashier->name}}</p>
                            <p class="w-[50%]">
                                {{
                                    $date = $cashier->created_at->format('F j, Y - g:i A');
                                }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        var main = document.getElementById('main')

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

        function openAddCashier(){
            var addModal = document.getElementById('add')
            var head = document.getElementById('head')
            var body = document.getElementById('body')
            addModal.classList.remove('hidden')
            head.style.filter = 'blur(5px)'
            body.style.filter = 'blur(5px)'
        }

        function closeCashierModal(){
            var addModal = document.getElementById('add')
            var head = document.getElementById('head')
            var body = document.getElementById('body')
            addModal.classList.add('hidden')
            head.style.filter = 'blur(0)'
            body.style.filter = 'blur(0)'
        }
    </script>
</body>
</html>