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
        <p class="text-lg text-white">Update Item</p>
    </div>
    <div class="w-full h-[93%] flex z-0">
        <div class="w-[5%] pt-10 bg-[#fefefe]">
            <div class="w-full">
                <a href="{{route('office.items_list')}}" class="block mx-auto py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="14" height="14" class="block mx-auto"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288 480 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-370.7 0 73.4-73.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-128 128z"/></svg>
                </a>
            </div>
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
                <button onclick="openItems()" class="w-full flex items-center justify-center h-auto py-4 border-l-[3px] border-[#ec407a] bg-[#f2f2f2]">
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
                <form action="{{route('office.logout')}}" method="POST" class="w-full flex items-center justify-center h-auto py-4">
                    @csrf
                    <button>
                        <i class="fa-solid fa-arrow-right-from-bracket fa-xl"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="w-[95%] bg-[#f2f2f2] z-0 p-7">
            <div class="w-1/2 flex mx-auto shadow-md text-sm">
                <form action="{{route('office.update_item')}}" class="w-full" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class=" bg-white rounded-md p-10 mb-5">
                        <div class="w-full flex items-center justify-between gap-5 mb-10">
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Item Name</label>
                                <input type="text" name="item_name" value="{{$item->item}}" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                            </div>
                            <div class="w-1/3">
                                <label for="" class="text-gray-500">Select Category</label>
                                <select name="category" id="" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                                    <option value="Groceries">Groceries</option>
                                    <option value="Meals">Meals</option>
                                </select>
                            </div>
                        </div>
                        <div class="w-full mb-10">
                            <label for="" class="text-gray-500">Description</label>
                            <textarea name="description" id=""rows="2" class="w-full mt-1 p-2 border border-[#eaeaea] focus:border-main outline-none rounded-md">{{$item->description}}</textarea>
                        </div>
                        <div class="w-full flex items-center justify-between gap-5">
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">SKU</label>
                                <input type="text" name="item_sku" value="{{$item->sku}}" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                            </div>
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">QR Value</label>
                                <input type="text" name="item_qr" value="{{$item->qr}}" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                            </div>
                        </div>
                    </div>
                    <div class=" bg-white rounded-md p-10">
                        <div class="w-full flex items-center justify-between gap-16 mb-10">
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Purchase Cost</label>
                                <input type="number" name="cost" value="{{$item->cost}}" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                            </div>
                            <div class="w-1/2">
                                <label for="" class="text-gray-500">Retail Value</label>
                                <input type="number" name="retail" value="{{$item->retail}}" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main">
                            </div>
                        </div>
                        <div class="w-ful flex gap-16">
                            <div class="w-full flex gap-5 items-center justify-end">
                                <button class="w-[100px] bg-slate-50 uppercase rounded-sm py-2 shadow-md font-medium">Cancel</button>
                                <button class="w-[100px] bg-green-500 uppercase rounded-sm py-2 shadow-md font-medium text-white">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>