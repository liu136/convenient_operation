<template>
    <div>
        <el-form ref="postForm" :model="postForm" :rules="rules" class="form-container">

            <!--提交申请栏 start-->
                <sticky :z-index="10" class-name="sub-navbar published">
                    <el-button :loading="submitFormLoading" type="warning" @click="submitForm">
                        1
                    </el-button>
                </sticky>
            <!--提交申请栏 end-->

            <!--填写活动配置 start-->
                <div  style="margin: 50px 1% 0 10%">

                  <el-row v-for="(item, index) in postForm.item_infos" style=" border-top: 0.5px dotted  #C03639 ;padding: 20px;">

                    <!--                      :rules="[{ required: true, message: $t('please_input_tips'),trigger: 'change'}]"-->
                    <el-col :span="5">
                      <el-form-item :label="'item_id'"   :label-width="label_width"   :prop="'item_infos.'+index+'.param1'">
                        <el-input   v-model="item.item_id" style="width:100%"  clearable   />
                      </el-form-item>
                    </el-col>

                    <!--                      :rules="[{ required: true, message: $t('please_input_tips'),trigger: 'change'}]"-->
                    <el-col :span="5" style="margin-left: 5%">
                      <el-form-item :label="'item_num'"  :label-width="label_width" :prop="'item_infos.'+index+'.param2'">
                        <el-input   v-model="item.item_num" style="width:100%"  clearable   />
                      </el-form-item>
                    </el-col>

                    <el-button v-if="index !== 0" size="mini" style="width: 8%;margin-left: 10%" type="danger" @click="handleDeleteRow(index)">
                      添加
                    </el-button>

                  </el-row>

                </div>
            <!--填写活动配置 end-->


        </el-form>
    </div>
</template>

<script>
import Sticky from '@/components/Sticky'
import {activity_type_list, date_type} from "@/config/activity_type_list";
import { pullData } from '@/api/server'
import {gift_type_list} from '@/config/game_gift'

export default {
        name: "add_activity",
        components:{Sticky},
        data(){
            return {
                label_width:'150px',
                input_width:'80%',
                submitFormLoading:false,
                rules:{
                    code_name:[{required:true,message:'我', trigger:'change'}],
                    // code_type:[{required:true, message:this.$t('required_select'), trigger:'change'}],
                    // code_amount:[{required:true,message:this.$t('required_fields'), trigger:'change'}],
                    param:[{validator: (rule, value, callback) => {
                                //如果tips的内容不等于 `无效,无需填写` 那就需要验证内容
                                if(this.isInput('param')){
                                    if(!value){
                                        callback(new Error('你'));
                                        return;
                                    }
                                }
                               callback();
                        },trigger: 'change'
                    }]

                },
                date_type:date_type,
                // gift_type_options: [{
                //     value: '1',
                //     label: '渠道礼包'
                // }, {
                //     value: '2',
                //     label: '活动礼包'
                // }],
                gift_type_options:gift_type_list,
                postForm:{
                    code_name:'',
                    // code_type:'',
                    limit_platform_type:'',
                    limit_cannel:'',
                    use_max_count:'',
                    generated_code_quantity:'',
                    time_out:'',
                    item_infos:[
                        {
                            // item_description:'',
                            // condition:'',
                            item_id:'',
                            item_num:'',
                        }
                    ],
                }
            }
        },
        methods:{
            submitForm(){
                const context = this
                this.$refs.postForm.validate(valid => {
                    if(valid){
                        pullData(this.postForm).then(res => {
                            if (res.code === 200) {
                                this.$store.dispatch('delVisitedViews', this.$route);
                                this.$router.go(-1);
                                context.$notify({
                                    title: 'success',
                                    message: res.message,
                                    type: 'success',
                                    duration: 2000
                                })
                            } else {
                                context.$notify({
                                    title: 'error',
                                    message: res.message,
                                    type: 'error',
                                    duration: 2000
                                })
                            }
                            context.submitFormLoading = false
                        }).catch(function () {
                            context.submitFormLoading = false
                        });

                    }}) ;
            },
            setContainServerId(val){
                this.postForm.real_server_ids = val.join(',');
            },
            setExceptServerId(val){
                this.postForm.except_svr_ids = val.join(',');
            },
            addRow(){
                this.postForm.item_infos.push({
                    item_id:'',
                    item_num:'',
                })            },
            handleDeleteRow(index){
                this.postForm.item_infos.splice(index, 1);
            },
        }
    }
</script>

<style scoped>

</style>
