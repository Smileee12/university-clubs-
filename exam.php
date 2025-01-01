<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
 body {
    background-color: aqua;
   
}

     .image_background{
        background-image: url('uploads/webProject1.webp');
    background-repeat: no-repeat;
     background-size: 90%; 
    background-position: center; 
    height: 95vh;
    width: 99vw;
    justify-content: center;
    align-items: center;
}
.container{
    justify-content: center;
    align-items: center;
    display: flex;
    transform: translate(0%, 50%);

}

</style>
<body>
    <div class="image_background">
    <div class="container">
        <div class="form">
        <input type="text" id="email" placeholder="Email Id">
        <input type="password" id="password" placeholder="password ">

        </div>
        <label for="remember"></label>
        <input type="checkbox" id="remember" name="remember" value="remember">
        <a href="#">forget password</a>
        <button>login</button>
        <p> don't have an account ? <a href="signUp.html">Sign uP</a></p>
       
    </div>
    </div>
</body>
</html>