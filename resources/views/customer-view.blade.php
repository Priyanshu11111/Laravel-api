<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <title>Views
        </title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
    </head>
    <table class="table">
  <thead>
    <tr>
      <th scope="col">id</th>
      <th scope="col">Firstname</th>
      <th scope="col">Lastname</th>
      <th scope="col">Emailid</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($customers as $customer)
    <tr>
      <th scope="row">{{$customer->id}}</th>
      <td>{{$customer->firstname}}</td>
      <td>{{$customer->lastname}}</td>
      <td>{{$customer->email}}</td>
      <td>
        <a href="{{url('customer/delete/')}}/{{$customer->id}}">
      <button class="btn btn-danger">Delete</button></a>
      <a href="{{url('customer/edit/')}}/{{$customer->id}}">
      <button class="btn btn-primary">Edit</button></a>
    </td>
    </tr>
    @endforeach
</table>
<div class="d-flex justify-content-center">
                {{ $customers->links() }}
            </div>
        </div>
    </body>
</html>
