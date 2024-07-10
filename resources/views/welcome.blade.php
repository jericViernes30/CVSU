<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite('resources/css/app.css')
</head>
<body class="relative h-screen bg-[#7e817f]">
  <div class="w-3/4 h-[80%] flex p-6 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-[#fefef8] rounded-3xl shadow-md backdrop-blur-sm border border-opacity-20">
    <div class="w-[60%] relative">
      <img src="{{asset('images/new_logo.png')}}" alt="" class="w-[80%] absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
    </div>
    <div class="w-[40%] flex flex-col justify-center">
      <p class="text-2xl font-semibold mb-4 text-[#3f3f3f]">Login your Cashier Account</p>
      <form action="{{route("auth_login")}}" method="POST" class="">
        @csrf
        <select name="POS" id="" class="w-full p-2 outline-none border-b-2 border-black mb-3">
          <option value="" selected disabled>Select Terminal</option>
          <option value="pos1">POS 1</option>
          <option value="pos2">POS 2</option>
        </select>
        <span class="w-full border-b border-[#d3d3d3] block mx-auto pt-4 mb-4"></span>
        <div class="w-full flex flex-col gap-1 mb-3">
          <label for="">Cashier Name</label>
          <input type="text" name="name" class="w-full p-2 outline-none rounded-md border border-[#727272] focus:border focus:border-main">
        </div>
        <div class="w-full flex flex-col gap-1 mb-4">
          <label for="">Password</label>
          <div class="relative">
            <img id="eyeOpen" src="{{asset('images/eye.png')}}" alt="" class="w-[4%] absolute right-3 top-1/2 transform -translate-y-1/2 hover:cursor-pointer">
            <input id="pass" type="password" name="password" class="w-full p-2 outline-none rounded-md border border-[#727272] focus:border focus:border-main">
          </div>
        </div>
        <input type="hidden" name="role" value="cashier">
        <button class="w-full rounded-md py-2 text-main border border-main hover:bg-main hover:text-white ease-in-out duration-100">
          Login
        </button>
      </form>
    </div>
  </div>
  <script>
      var toggleOpen = document.getElementById('eyeOpen')
      var pass = document.getElementById('pass')

      toggleOpen.addEventListener('click', function(){
        if (pass.type === 'password') {
          pass.type = 'text';
          toggleOpen.src = "{{asset('images/eye-inv.png')}}";
        } else {
          pass.type = 'password';
          toggleOpen.src = "{{asset('images/eye.png')}}";
        }
      })
  </script>
</body>
</html>