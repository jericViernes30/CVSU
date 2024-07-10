<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5bf9be4e76.js" crossorigin="anonymous"></script>
    @vite('resources/css/app.css')
    <title>Back Office</title>
</head>
<body class="w-full h-screen bg-[#fefefe]">
    <div class="w-full flex items-center h-[7%] bg-[#db121c] px-10">
        <p class="text-lg text-white">Create Item</p>
    </div>
    <div class="w-full h-[93%] flex z-0">
        <div class="w-[5%] pt-10 bg-[#fefefe]">
            <div class="flex w-2/3 mx-auto flex-col items-center justify-center pb-4 mb-3">
                <img src="{{asset('images/logo-transparent.png')}}" alt="">
            </div>
            <div class="w-full relative">
                <button onclick="openDashboard()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/chart-new.png')}}" alt="" class="w-[30px] h-auto">
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
                <a href="{{route('office.items_list')}}" class="w-full flex items-center justify-center h-auto py-4 bg-[#f5a7a4]">
                    <img src="{{asset('images/prod-red.png')}}" alt="" class="w-[30px] h-auto">
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
        <div class="w-[95%] bg-[#f2f2f2] z-0 p-7">
            <div class="w-1/2 flex mx-auto shadow-md text-sm">
                <form action="{{route('office.add_item')}}" class="w-full" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class=" bg-white rounded-md p-10 mb-5">
                        <div class="w-full flex items-center justify-between gap-5 mb-10">
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Item Name</label>
                                <input type="text" name="item_name" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                            </div>
                            <div class="w-1/3">
                                <label for="" class="text-gray-500">Select Category</label>
                                <select name="category" id="" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                                    <option value="dry_goods">Dry Goods</option>
                                    <option value="wet_goods">Wet Goods</option>
                                </select>
                            </div>
                        </div>
                        <div class="w-full mb-10">
                            <label for="" class="text-gray-500">Supplier</label>
                            <select name="supplier" class="w-full mt-1 p-2 border border-[#eaeaea] focus:border-main outline-none rounded-md">
                                @foreach ($suppliers as $supplier)
                                    <option value="{{$supplier->name}}">{{$supplier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full flex items-center justify-between gap-5">
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Product unit</label>
                                <select name="product_unit" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                                    <option value="pc">Per pc</option>
                                    <option value="kg">Per kg</option>
                                    <option value="pack">Per pack</option>
                                    <option value="sack">Per sack</option>
                                </select>
                            </div>
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Barcode</label>
                                <input type="text" name="barcode" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main" autofocus="false">
                            </div>
                        </div>
                    </div>
                    <div class=" bg-white rounded-md p-10">
                        <div class="w-full flex items-center justify-between gap-16 mb-10">
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Purchase Cost</label>
                                <input type="number" name="cost" step="any" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                            </div>
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Retail Value</label>
                                <input type="number" name="retail" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                            </div>
                        </div>
                        <div class="w-ful flex gap-16">
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Item Image</label>
                                <input type="file" name="item_image" class="mt-1">
                            </div>
                            <div class="w-1/2 flex gap-5 items-center justify-end">
                                <button class="w-[100px] bg-main uppercase rounded-sm py-2 shadow-md font-medium text-black text-xs">Add Item</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var qrInput = document.querySelector('input[name="item_qr"]');
    
            qrInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Prevent default Enter key behavior
                }
            });
        });
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
    </script>
</body>
</html>