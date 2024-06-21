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
    <div class="w-full flex items-center h-[7%] bg-[#db121c] px-10">
        <p class="text-lg text-white">Update Item</p>
    </div>
    <div class="w-full h-[93%] flex z-0">
        <div class="w-[5%] pt-10">
            <div class="w-full relative">
                <button onclick="openDashboard()" class="w-full flex items-center justify-center h-auto py-4">
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
                <button onclick="openItems()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/products.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="items_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.items_list')}}">Items List</a>
                        <a href="">Category</a>
                        {{-- {{route('office.category')}} --}}
                    </div>
                </div>
            </div>
            <div class="w-full relative">
                <button onclick="openInventoryOptions()" class="w-full flex items-center justify-center h-auto py-4 border-l-[3px] border-[#039be5] bg-[#f2f2f2]">
                    <img src="{{asset('images/inventory.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="inventory_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="#">Stocks Adjustment</a>
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
        <div class="w-[95%] bg-[#f2f2f2] z-0 p-7">
            <div class="w-1/3 flex mx-auto shadow-md text-sm">
                <form action="{{route('office.update_stocks')}}" class="w-full" method="get" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white rounded-md p-10">
                        <div class="w-full flex items-center justify-between gap-5 mb-10">
                            <div class="w-1/2">
                                <p class="text-3xl font-medium text-gray-800">{{$item->item}}</p>
                            </div>
                        </div>
                        <div class="w-full mb-10">
                            <label for="" class="text-gray-500">Select an option</label>
                            <select name="option" id="option" class="w-full p-2 outline-none border rounded-md mb-5 text-center focus:border-b-2 focus:border-main mt-1">
                                <option value="none" default>--    Select option   --</option>
                                <option value="increase">Increase stocks</option>
                                <option value="decrease">Decrease stocks</option>
                            </select>
                            <label for="" class="text-gray-500">Select reason</label>
                            <select name="reason" id="reason" class="w-full p-2 outline-none border rounded-md mb-5 focus:border-b-2 focus:border-main mt-1">
                            </select>
                        </div>
                        <div id="fields" class="w-full">
                            <div class="w-full mb-10">
                                <label for="" class="text-gray-500">Quantity</label>
                                <input type="text" name="quantity" value="{{$item->quantity}}" class="w-full mt-1 px-2 py-1 outline-none border-b-2 bg-slate-50 border-[#eaeaea] focus:border-b-2 focus:border-main text-center">
                            </div>
                            <div class="w-full flex gap-5 items-center justify-end">
                                <button class="w-[100px] bg-slate-50 uppercase rounded-sm py-2 shadow-md font-medium">Cancel</button>
                                <button class="w-[100px] bg-green-500 uppercase rounded-sm py-2 shadow-md font-medium text-white">Save</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="item" value="{{$item->item}}">
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('#option').change(function(){
                var option = $(this).val()
                if(option == "increase"){
                    var reasons = `
                        <option value='New stocks'>New stocks</option>
                        <option value='Returned Item'>Returned Item</option>
                        <option value='Not included in previous inventory'>Not included in previous inventory</option>
                    `
                    $('#reason').append(reasons)
                } else if(option == "decrease"){
                    var reasons = `
                        <option value='Pull out'>Pull out</option>
                        <option value='Out of season'>Out of season</option>
                        <option value='Expired'>Expired</option>
                    `
                    $('#reason').append(reasons)
                }
            })
        })
    </script>
</body>
</html>