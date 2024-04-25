<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    @vite('resources/css/app.css')
    <title>Dashboard</title>
</head>
<body class="w-full h-screen bg-[#fefefe]">
    <div class="w-2/4 mx-auto hidden absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50" id="scanner">
        <video class="mx-auto" id="preview" width="1%"></video><br>
    </div>
    {{-- Top bar --}}
    <div class="w-full flex items-center h-[8%] px-20 border-b border-bd">
        <div class="w-1/6">
            <p class="text-lg font-semibold">MAMITA'S</p>
        </div>
        <div class="w-10/12">
            <form action="">
                <input type="search" name="search" placeholder="Search here ..." class="py-1 px-4 outline-none w-1/2 border border-bd rounded-full">
            </form>
        </div>
    </div>
    {{-- main --}}
    <div class="w-full flex h-[92%]">
        {{-- navigations --}}
        <div class="w-[5%] border-r border-bd py-6">
            <div class="flex w-full flex-col items-center justify-center mb-8">
                <img src="{{asset('images/home-hover.png')}}" alt="Home Icon" class="w-1/3">
                <p class="text-xs text-main">Home</p>
            </div>
            <a href="{{route('cashier')}}" class="flex w-full flex-col items-center justify-center mb-8">
                <img src="{{asset('images/cashier.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">Cashier</p>
            </a>
            <a href="{{route('history')}}" class="flex w-full flex-col items-center justify-center">
                <img src="{{asset('images/history.png')}}" alt="Cashier Icon" class="w-1/3">
                <p class="text-xs">History</p>
            </a>
        </div>
        {{-- POS --}}
        <div class="w-[95%] flex">
            {{-- selection --}}
            <div class="w-3/4 p-6 bg-[#e5e5e5]">
                <div class="w-1/2 mb-6">
                    <form action="" method="GET" class="w-full flex justify-between items-center py-2 px-5 text-sm bg-[#fefefe] rounded-full">
                        @csrf
                        <button class="text-main">Breakfast</button>
                        <button class="hover:text-main transition duration-100 ease-in-out">Lunch</button>
                        <button class="hover:text-main transition duration-100 ease-in-out">Beverages</button>
                        <button class="hover:text-main transition duration-100 ease-in-out">Extras</button>
                    </form>
                </div>
                <div id="foods" class="w-full grid grid-cols-5 grid-rows-3 gap-4 h-[90%]">
                    @foreach ($menus as $menu)
                       <button class="menu-button flex flex-col rounded-2xl shadow-lg bg-[#fefefe] p-4 items-center text-sm" data-food-name="{{$menu->food_name}}" data-price="{{$menu->price}}">
                            <div class="w-[100px] h-[100px] rounded-full block mx-auto mb-2">
                                <img src="{{asset('images/menu/'.$menu->image_name).'.jpg'}}" alt="{{$menu->image_name}}" class="w-full h-full object-cover rounded-full">
                            </div>
                            <p class="">{{$menu->food_name}}</p>
                            <p class="font-medium">&#8369; {{$menu->price}}.00</p>
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
                            <input type="text" name="customer" placeholder="Customer Name" class="w-full border border-bd rounded-full outline-none py-2 px-4">
                        </div>
                        <div class="w-1/4 text-right">
                            <p>#1-{{$ticket}}</p>
                        </div>
                        <div class="w-1/4 flex justify-end items-center">
                            <button onclick="clearOrder()" class="w-[25px] h-[25px] rounded-full border border-black flex items-center justify-center">
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
                                <p id="total">&#8369; 0.00</p>
                            </div>
                            <div class="w-full flex justify-between mb-6">
                                <p class="text-xl font-medium">Payable Amount: </p>
                                <p id="payable" class="text-xl font-medium">&#8369; 0.00</p>
                            </div>
                        </div>
                        <div class="w-full flex items-center justify-between gap-2 text-sm text-white">
                            <button name="action" value="proceed" class="w-full rounded-full py-4 bg-proceed">
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
        document.addEventListener('DOMContentLoaded', function () {
            const backgroundElement = document.getElementById("background");
            const scanner = new Instascan.Scanner({ video: document.getElementById('preview'), continuous: true });

            // const audio = new Audio('../audio/hentai.mp3');

            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    alert('No cameras found!');
                }
            }).catch(function (e) {
                console.error(e);
            });

            // function playBeepSound() {
            //     // Play the preloaded beep sound
            //     audio.currentTime = 0;
            //     audio.play();
            // }

            scanner.addListener('scan', function (qr) {
                // playBeepSound();
                // var url = 'reservations.php?ticket=' + encodeURIComponent(qr);
                
                // Delay the navigation by 1 second (1000 milliseconds)
                // setTimeout(function() {
                //     // window.location.href = url;
                // }, 3000);// Split the string by comma
                var values = qr.split(',');

                // Now values[0] contains the first value, and values[1] contains the second value
                var firstValue = values[0];
                var secondValue = values[1];

                // You can then use these values as needed
                // playBeepSound();

                // For example, you can log them to the console
                console.log("First Value:", firstValue);
                console.log("Second Value:", secondValue);

                addToOrders(firstValue, secondValue);
                updateOrdersDisplay();
            });
        });

        function addToOrders(firstValue, secondValue) {
            orders.push({ foodName: firstValue, price: secondValue });
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'F9') {
                let openScanner = document.getElementById('scanner');
                openScanner.style.display = "block"
                console.log('Scanner Opened')
            }
        });

        var orders = [];
    
        function updateOrdersDisplay() {
            var ordersContainer = document.getElementById('orders');
            var payableElement = document.getElementById('payable')
            var totalElement = document.getElementById('total');
            var total = 0; // Initialize total price to zero

            // Clear previous content
            ordersContainer.innerHTML = '';

            // Loop through orders array and display each order
            orders.forEach((order, index) => {
                const totalPrice = orders.reduce((total, order) => total + parseInt(order.price), 0);
                totalElement.textContent = '₱ ' + totalPrice + '.00';
                payableElement.textContent = '₱ ' + totalPrice + '.00';
                var orderDiv = document.createElement('div');
                orderDiv.className = 'w-full flex justify-between items-center px-4 py-2';

                var orderedFoodElement = document.createElement('p');
                orderedFoodElement.textContent = order.foodName;
                orderDiv.appendChild(orderedFoodElement);

                var priceElement = document.createElement('p');
                priceElement.textContent = '₱ ' + order.price + '.00';
                orderDiv.appendChild(priceElement);

                var inputElement = document.createElement('input');
                inputElement.setAttribute('type', 'hidden');
                inputElement.setAttribute('name', 'food_name[]');
                inputElement.setAttribute('value', order.foodName);
                orderDiv.appendChild(inputElement);

                var totalPriceElement = document.createElement('input');
                totalPriceElement.setAttribute('type', 'hidden');
                totalPriceElement.setAttribute('name', 'total');
                totalPriceElement.setAttribute('value', totalPrice);
                orderDiv.appendChild(totalPriceElement);

                ordersContainer.appendChild(orderDiv);
            });
        }



        function clearOrder(){
            event.preventDefault();
            orders.length = 0;

            // Clear the contents of ordersContainer
            var ordersContainer = document.getElementById('orders');
            ordersContainer.innerHTML = '';
        }
    
        document.querySelectorAll('.menu-button').forEach(button => {
            button.addEventListener('click', function() {
                var foodName = this.getAttribute('data-food-name');
                var price = this.getAttribute('data-price');
                orders.push({ foodName: foodName, price: price });
                updateOrdersDisplay(); // Update display when a new order is added
            });
        });
    </script>
</body>
</html>