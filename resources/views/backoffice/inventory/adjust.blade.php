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
                <button onclick="openItems()" class="w-full flex items-center justify-center h-auto py-4">
                    <img src="{{asset('images/products.png')}}" alt="" class="w-[30px] h-auto">
                </button>
                <div id="items_options" class="hidden w-[200px] absolute left-20 top-0 z-10 bg-slate-50 p-3 text-sm">
                    <div class="w-full flex flex-col gap-3">
                        <a href="{{route('office.dashboard')}}">Sales Summary</a>
                        <a href="#">Sales by item</a>
                        <a href="#">Sales by employee</a>
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