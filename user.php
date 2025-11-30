<?php 
class User{
    private $file="users.json";
    private $users=[];


public function __construct(){
    if(file_exists($this->file)){
        $data=json_decode(file_get_contents($this->file),true);
        $this->users=$data?$data:[];
    }
    else{
        $this->users=[];
    }
}

public function register($username,$password,$firstName="",$lastName="",$email="",$birthday="",$age="",$address="",$phone=""){
     foreach($this->users as $user){
        if($user["username"]==$username){
            return "Username is already used";
            exit();
        }
     }
     
     $this->users[]=[
        "username"=>$username,
        "password"=>password_hash($password,PASSWORD_DEFAULT),
        "firstName"=>$firstName,
        "lastName"=>$lastName,
        "email"=>$email,
        "birthday"=>$birthday,
        "age"=>$age,
        "address"=>$address,
        "phone"=>$phone
     ];
     file_put_contents($this->file,json_encode($this->users,JSON_PRETTY_PRINT));
     return "Registration Successful";
}

public function login($username,$password){
    foreach($this->users as $user){
        if($user["username"]==$username){
            if(password_verify($password,$user["password"])){
                return "Login Successful";
            }
            else{
                return "Invalid Password";
            }
        }
    }
    return "Username not found";
}

public function getAllUsers(){
    return $this->users;
}
}
?>