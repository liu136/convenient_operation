/**
 * 所有api接口
 * @param permission 是否需要权限才能访问，该字段用户用户权限管理功能
 * **/
export default {
  pull_data:{
    url:'/server/pull_data',
    method:'post',
    permission:true,
    description:'添加后台游戏活动配置'
  }
}
