<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>
<body class="w-full h-screen relative bg-[#ffd962]">
    <div class="w-2/4 mx-auto absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50 hidden" id="scanner">
        <video class="mx-auto" id="preview" width="50%"></video><br>
    </div>
    <div id="quantity-div" class="w-1/6 p-4 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-red-500 hidden">
        <p>Input quantity</p>
        <input type="number" id="quantity-input" class="w-full py-2 text-center rounded-sm outline-none border" value="1">
        <button id="submit-quantity" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-sm">Submit</button>
    </div>
    <div class="w-1/5 mx-auto hidden absolute bottom-0 left-0 z-50" id="scanner">
        <video class="mx-auto" id="preview" width="100%"></video><br>
    </div>
    {{-- Top bar --}}
    <div class="w-full flex items-center h-[8%] px-20 border-b border-bd">
        <div class="w-1/6">
            <div class="">
                <img src="{{asset('images/logo2.png')}}" alt="" class="w-1/2">
            </div>
        </div>
        <div class="w-10/12 flex items-center">
            <input id="search" type="search" name="search" placeholder="Search here ..." class="py-1 px-4 outline-none w-[40%] border border-bd rounded-full bg-[#e5e5e5]">
        </div>
    </div>
    {{-- main --}}
    <div class="w-full flex h-[92%]">
        {{-- navigations --}}
        <div class="w-[5%] py-6">
            <div class="flex w-full flex-col items-center justify-center py-4 bg-[#e5e5e5]">
                <img src="{{asset('images/home.png')}}" alt="Home Icon" class="w-1/3">
                <p class="text-xs text-black">Home</p>
            </div>
            <a href="{{route('cashier')}}" class="flex w-full flex-col items-center justify-center py-4">
                <img src="{{asset('images/cashier.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">Cashier</p>
            </a>
            <a href="{{route('history')}}" class="flex w-full flex-col items-center justify-center py-4">
                <img src="{{asset('images/history.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">History</p>
            </a>
            <a href="{{route('office.login')}}" target="__blank" class="flex w-full flex-col items-center justify-center">
                <img src="{{asset('images/ranking.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">Back Office</p>
            </a>
        </div>
        {{-- POS --}}
        <div class="w-[95%] flex">
            {{-- selection --}}
            <div class="w-3/4 p-6 bg-[#e5e5e5]">
                <input id="barcode" type="search" name="barcode" placeholder="Barcode" class="py-1 px-4 outline-none w-[20%] border-b border-black mb-2">
                <div id="foods" class="w-full grid grid-cols-5 grid-rows-6 gap-4 h-[90%]">
                    @foreach ($menus as $menu)
                       <button class="menu-button flex flex-col rounded-2xl shadow-lg bg-[#fefefe] p-4 items-center justify-center text-sm hover:bg-main hover:text-white" data-food-name="{{$menu->item}}" data-price="{{$menu->retail}}">
                            <p class="">{{$menu->item}}</p>
                            <p class="font-medium">&#8369; {{$menu->retail}}.00</p>
                       </button>
                    @endforeach
                </div>
            </div>
            {{-- ticket --}}
            <div id="ticket" class="w-1/4">
                <form action="{{route('ticket_details')}}" class="w-full" method="get">
                    @csrf
                    <div class="w-full flex justify-between p-2 border-b border-bd items-center">
                        <div class="w-1/2">
                            <input id="customer" type="text" name="customer" placeholder="Customer Name" class="w-full border border-bd rounded-full outline-none py-2 px-4 bg-[#e5e5e5]">
                        </div>
                        <div class="w-1/4 text-right">
                            <p>#1-{{$ticket}}</p>
                        </div>
                        <div class="w-1/4 flex justify-end items-center">
                            <button type="button" id="clear" class="w-[25px] h-[25px] rounded-full border border-black flex items-center justify-center">
                                <img src="{{asset('images/delete.png')}}" alt="Delete Button" class="w-3/4">
                            </button>
                        </div>
                    </div>
                    <div id="orders" class="w-full h-[450px] overflow-y-auto border-b border-bd">

                    </div>
                    <div  class="w-full p-2">
                        <div class="w-full">
                            <div class="w-full flex justify-between mb-4">
                                <p>Sub-total: </p>
                                <p>&#8369; <span id="total">0.00</span></p>
                            </div>
                            <div class="w-full flex justify-between mb-6">
                                <p class="text-lg font-medium">Payable Amount: </p>
                                <p class="text-lg font-medium">&#8369; <span id="payable">0.00</span></p>
                            </div>
                        </div>
                        <div class="w-full flex items-center justify-between gap-2 text-sm text-white">
                            <button name="action" value="proceed" class="w-full rounded-full py-4 bg-main">
                                Proceed
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="ticket" value="{{$ticket}}">
                </form>
            </div>
        </div>
    </div>

    <script>
        const itemsData = {
            @foreach($menus as $menu)
                "{{$menu->qr}}": { item: "{{$menu->item}}", price: {{$menu->retail}} }{{ !$loop->last ? ',' : '' }}
            @endforeach
        };
    
        let orders = [];
        let currentQuantity = 1;
    
        $(document).ready(function() {
            const customerName = $('#customer')
            const barcodeInput = $('#barcode');
            const quantityInput = $('#quantity-input');
            const quantityDiv = $('#quantity-div');
            const submitQuantityButton = $('#submit-quantity');
            const backgroundElement = document.getElementById("background");
            const scanner = new Instascan.Scanner({ video: document.getElementById('preview'), continuous: true });
    
            // Function to keep the input field focused
            function keepFocus() {
                setTimeout(() => { barcodeInput.focus(); }, 10);
            }
    
            // Initially set focus to the input field
            keepFocus();

            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    alert('No cameras found!');
                }
            }).catch(function (e) {
                console.error(e);
            });

            scanner.addListener('scan', function (qr) {
                
                var values = qr.split(',');
                var firstValue = values[0];
                var secondValue = parseFloat(values[1]);

                // For example, you can log them to the console
                console.log("First Value:", firstValue);
                console.log("Second Value:", secondValue);

                addToOrders(firstValue, secondValue);
                updateOrdersDisplay();
            });
    
            // Submit quantity button click handler
            submitQuantityButton.on('click', function() {
                let quantity = parseInt(quantityInput.val());
                if (!isNaN(quantity) && quantity > 0) {
                    currentQuantity = quantity;
                    quantityDiv.addClass('hidden');
                    keepFocus();
                } else {
                    alert('Please enter a valid quantity');
                }
            });
    
            // Listen for the barcode input
            barcodeInput.on('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    let barcodeValue = barcodeInput.val().trim();
    
                    if (itemsData[barcodeValue]) {
                        let orderItem = itemsData[barcodeValue];
                        for (let i = 0; i < currentQuantity; i++) {
                            addToOrders(orderItem.item, orderItem.price);
                        }
    
                        updateOrdersDisplay();
                        currentQuantity = 1;  // Reset the quantity to 1
                        quantityInput.val(1);  // Reset the quantity input to 1
                    } else {
                        console.log('Item not found for barcode:', barcodeValue);
                    }
    
                    barcodeInput.val(''); // Clear the input field for the next scan
                    keepFocus(); // Ensure the input field stays focused
                }
            });
    
            // Toggle quantity input on specific key press (e.g., `)
            $(document).on('keydown', function(event) {
                if (event.key === '`') {
                    quantityDiv.toggleClass('hidden');
                    quantityInput.focus();
                }
            });
    
            // Show scanner on F9 key press (if you have a scanner video element)
            $(document).on('keydown', function(event) {
                if (event.key === 'F9') {
                    $('#scanner').toggle('hidden');
                }
            });

            // Function to refocus barcode input when clicking outside
            // $(document).on('click', function(event) {
            //     if (!$(event.target).closest('#search').length) {
            //         keepFocus();
            //     }
            // });
    
            function addToOrders(firstValue, secondValue) {
                orders.push({ foodName: firstValue, price: secondValue });
            }
    
            function updateOrdersDisplay() {
                var ordersContainer = $('#orders');
                var payableElement = $('#payable');
                var totalElement = $('#total');
                var total = 0;
    
                ordersContainer.html('');
    
                orders.forEach((order) => {
                    total += order.price;
    
                    var orderDiv = $('<div></div>').addClass('w-full flex justify-between items-center px-4 py-2');
    
                    var orderedFoodElement = $('<p></p>').text(order.foodName);
                    orderDiv.append(orderedFoodElement);
    
                    var priceElement = $('<p></p>').text(order.price.toFixed(2));
                    orderDiv.append(priceElement);
    
                    var inputElement = $('<input>').attr({ type: 'hidden', name: 'food_name[]', value: order.foodName });
                    orderDiv.append(inputElement);
    
                    var totalPriceElement = $('<input>').attr({ type: 'hidden', name: 'total', value: total.toFixed(2) });
                    orderDiv.append(totalPriceElement);
    
                    ordersContainer.append(orderDiv);
                });
    
                totalElement.text(total.toFixed(2));
                payableElement.text(total.toFixed(2));
            }
    
            function hideQuantityDiv() {
                $('#quantity-div').addClass('hidden');
            }
    
            // Event listener for button clicks
            $('.menu-button').on('click', function() {
                let foodName = $(this).data('food-name');
                let price = parseFloat($(this).data('price'));
    
                if (foodName && !isNaN(price)) {
                    addToOrders(foodName, price);
                    updateOrdersDisplay();
                    keepFocus(); // Ensure the input field stays focused
                } else {
                    console.log('Invalid item data:', foodName, price);
                }
            });
    
            // Trigger barcode scanning automatically
            keepFocus();

            $('#clear').on('click', function(event){
                event.preventDefault();
                orders = [];
                updateOrdersDisplay();
            })
        });
    </script>
    
    
    
</body>
</html>