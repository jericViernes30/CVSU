<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrator Login</title>
  @vite('resources/css/app.css')
</head>
<body class="flex flex-col gap-5 items-center justify-center relative h-screen bg-[#7e817f]">
  <div class="w-3/4 h-[80%] flex p-6 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-[#fefef8] rounded-3xl shadow-md backdrop-blur-sm border border-opacity-20">
    <div class="w-[60%] relative">
      <img src="{{asset('images/new_logo.png')}}" alt="" class="w-[80%] absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
    </div>
    <div class="w-[40%] flex flex-col justify-center">
      
  @if(session('error'))
  <div class="w-full bg-main px-4 py-2 flex items-center gap-4 rounded-md mb-2">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="14" height="14" fill="white"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M173.898 439.404l-166.4-166.4c-7.8-7.8-7.8-20.4 0-28.2s20.4-7.8 28.2 0L192 397.898 477.3 112.604c7.8-7.8 20.4-7.8 28.2 0s7.8 20.4 0 28.2l-320 320c-7.8 7.8-20.4 7.8-28.2 0z"/></svg>
      <p class="text-white">{{ session('error') }}</p>
  </div>
@endif
      <p class="text-2xl font-semibold mb-4 text-[#3f3f3f]">Login your Admin Account</p>
      <form action="{{route("office.login.post")}}" method="POST">
        @csrf
        <div class="w-full flex flex-col gap-1 mb-3">
          <label for="">Admin ID</label>
          <input type="text" name="name" class="w-full p-2 outline-none rounded-md border border-black">
        </div>
        <div class="w-full flex flex-col gap-1 mb-3">
          <label for="">Password</label>
          <div class="relative">
            <img id="eyeOpen" src="{{asset('images/eye.png')}}" alt="" class="w-[4%] absolute right-3 top-1/2 transform -translate-y-1/2 hover:cursor-pointer">
            <input id="pass" type="password" name="password" class="w-full p-2 outline-none rounded-md border border-[#727272] focus:border focus:border-main">
          </div>
        </div>
        <input type="hidden" name="role" value="admin">
        <button class="w-full rounded-md py-2 text-main border border-main hover:bg-main hover:text-white ease-in-out duration-100">
          Login
        </button>
      </form>
    </div>
  </div>
  {{-- <div class="w-1/4 p-6 bg-[#ffffffb2] rounded-3xl shadow-md backdrop-blur-sm border border-opacity-20">
    <img src="{{asset('images/logo2.png')}}" alt="" class="block mx-auto w-1/2">
    <form action="{{route("office.login.post")}}" method="POST">
      @csrf
      <div class="w-full flex flex-col gap-1 mb-3">
        <label for="">Admin ID</label>
        <input type="text" name="name" class="w-full p-2 outline-none rounded-full border border-black">
      </div>
      <div class="w-full flex flex-col gap-1 mb-3">
        <label for="">Password</label>
        <input type="password" name="password" class="w-full p-2 outline-none rounded-full border border-black">
      </div>
      <input type="hidden" name="role" value="admin">
      <button class="w-1/2 rounded-full py-2 text-white bg-main block mx-auto">
        Login
      </button>
    </form>
  </div> --}}
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