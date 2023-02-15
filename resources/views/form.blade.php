<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <title>Forms
        </title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
    </head>
    <body class="antialiased">
        <form action="" method="post">
            @csrf 
            <div class="container">
                <h1 class="text-center">Registration Form</h1>
                <div class="form group">
                    <label for="">Firstname</label>
                    <input type ="text" name="firstname" id="" class="form-control" placeholder="" value="{{old('name')}}"/> 
                    <span class='text-danger'>
                        @error('firstname')
                        {{$message}}
                        @enderror
                    </span>
                    <br>
                    <label for="">Lastname</label>
                    <input type ="text" name="lastname" id="" class="form-control" placeholder="" value="{{old('name')}}"/> 
                    <span class='text-danger'>
                        @error('lastname')
                        {{$message}}
                        @enderror
                    </span>
                    <br>
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type ="email" name="email" id="" class="form-control" placeholder="" value="{{old('email')}}"/>
                <span class='text-danger'>
                        @error('email')
                        {{$message}}
                        @enderror
                    </span>
                    <br>
            </div>
            <div class="form-group">
                <label for="">Password</label>
                <input type="Password" name="password" id="" class="form-control" placeholder=""/>
                <span class='text-danger'>
                        @error('password')
                        {{$message}}
                        @enderror
                    </span>
                    <br>
                <label for="">Confirm Password</label>
                <input type="Password" name="password_confirmation" id="" class="form-control" placeholder=""/>
                <span class='text-danger'>
                        @error('password_confirmation')
                        {{$message}}
                        @enderror
                    </span>
            </div>
            <div class="form-group">
            <br>
          <br><button class="btn btn-primary">
            Submit
            </button>
            </div>
        </form>
     </body>
</html>
