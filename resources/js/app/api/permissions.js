import request from '@/app/utils/request';

export class PermissionsResource{
    get(){
        return request({
            url: '/permissions',
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
