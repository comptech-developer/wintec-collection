<?php

namespace App\Enums;
enum IncomingRequestEnum :string
{
  
    case VALIDATION ='validation';
    case NOTIFICATION ='notification';
    case CALLBACK = 'callback';
}