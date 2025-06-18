<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case VIEW_ROOM = 'view room';
    case EDIT_ROOM = 'edit room';

    case VIEW_BRANCH = 'view branch';
    case EDIT_BRANCH = 'edit branch';

    case VIEW_SERVICE = 'view service';
    case EDIT_SERVICE = 'edit service';

    case VIEW_ROOM_TYPE = 'view room type';
    case EDIT_ROOM_TYPE = 'edit room type';

    case VIEW_ROOM_PRICING = 'view room pricing';
    case EDIT_ROOM_PRICING = 'edit room pricing';

    case VIEW_CUSTOMER_TYPE = 'view customer type';
    case EDIT_CUSTOMER_TYPE = 'edit customer type';

    case VIEW_CUSTOMER = 'view customer';
    case EDIT_CUSTOMER = 'edit customer';

    case VIEW_STAFF = 'view staff';
    case EDIT_STAFF = 'edit staff';


    case VIEW_BOOKING = 'view booking';
    case CREATE_BOOKING = 'create booking';
    case CANCEL_BOOKING = 'cancel booking';

    case VIEW_BOOKING_SERVICES = 'view booking services';
    case EDIT_BOOKING_SERVICES = 'edit booking services';

    case VIEW_ROOM_STATUS_HISTORIES = 'view room status histories';
    
    case VIEW_MAINTENANCES = 'view maintenance';
    case EDIT_MAINTENANCES = 'edit maintenance';

    case VIEW_REVIEW = 'view review';
    case DELETE_REVIEW = 'delete review';

    case VIEW_ROLE = 'view role';
    case EDIT_ROLE = 'edit role';

    case VIEW_PERMISSION = 'view permission';
    case EDIT_PERMISSION = 'edit permission';


    //...
    


}