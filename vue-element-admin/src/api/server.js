import request from '@/utils/request'
import api from '@/config/api_url'
/**
 * 拉取数据
 * **/
export function pullData(data){
  return request({
    url:api.pull_data.url,
    method:api.pull_data.method,
    data
  });
}
