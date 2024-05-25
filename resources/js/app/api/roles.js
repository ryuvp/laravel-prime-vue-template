import request from '@/app/utils/request';

export class RolesResource{
    get(){
        return request({
            url: '/roles',
            method: 'get',
        })
    }
    post(){

    }
    update(){

    }
    delete(){

    }
}
