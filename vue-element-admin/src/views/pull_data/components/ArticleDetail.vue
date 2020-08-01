<script src="../../../../../../swoole/live/public/static/live/js/live.js"></script>
<template>
  <div class="createPost-container">
    <el-form ref="postForm" :model="postForm" :rules="rules" class="form-container">

      <sticky :z-index="10" :class-name="'sub-navbar '+postForm.status">
        <el-button v-loading="loading" style="margin-left: 10px;" type="success" @click="submitForm">
          拉取数据
        </el-button>
      </sticky>

      <div class="createPost-main-container">
        <el-row>
          <el-col :span="24">
            <el-form-item prop="ip">
              <MDinput v-model="postForm.ip" :maxlength="100" name="name" required>
                目标机器的IP
              </MDinput>
            </el-form-item>
            <el-form-item prop="database">
              <MDinput v-model="postForm.database" :maxlength="100" name="name" required>
                目标数据库名
              </MDinput>
            </el-form-item>
            <el-form-item prop="local_database">
              <MDinput v-model="postForm.local_database" :maxlength="100" name="name" required>
                本地wyt数据库名的数字
              </MDinput>
            </el-form-item>
            <el-form-item prop="actor_id">
              <MDinput v-model="postForm.actor_id" :maxlength="100" name="name" required>
                需要修改的角色id
              </MDinput>
            </el-form-item>
            <el-dropdown-item>
              该玩家所在的游戏是否存在虚拟服：
              <el-radio-group v-model="postForm.virtual" style="padding: 10px;">
                <el-radio :label="1">
                  是
                </el-radio>
                <el-radio :label="0">
                  否
                </el-radio>
              </el-radio-group>
            </el-dropdown-item>

            <el-dialog v-el-drag-dialog :visible.sync="dialogTableVisible" title="拉取数据返回的结果" @dragDialog="handleDrag">
              <el-table :data="gridData">
                <el-table-column property="data" label="拉取数据返回的结果" />
              </el-table>
            </el-dialog>

          </el-col>
        </el-row>
      </div>
    </el-form>
  </div>
</template>

<script>
import MDinput from '@/components/MDinput'
import Sticky from '@/components/Sticky' // 粘性header组件
import { fetchArticle } from '@/api/article'
import { searchUser } from '@/api/remote-search'
import  { pullData } from '@/api/server'
import elDragDialog from "@/directive/el-drag-dialog";

const defaultForm = {
  ip: '', // 目标机器的IP
  database: '', // 目标数据库名
  local_database: '', // 本地wyt数据库名的数字
  actor_id: '', // 需要修改的角色id
  virtual: 0 // 该玩家所在的游戏是否存在虚拟服
}

export default {
  name: 'ArticleDetail',
  directives: { elDragDialog },
  components: { MDinput, Sticky },
  props: {
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  data() {
    const validateRequire = (rule, value, callback) => {
      if (value === '') {
        this.$message({
          message: rule.field + '为必传项',
          type: 'error'
        })
        callback(new Error(rule.field + '为必传项'))
      } else {
        callback()
      }
    }
    return {
      postForm: Object.assign({}, defaultForm),
      loading: false,
      userListOptions: [],
      rules: {
        ip: [{ validator: validateRequire }],
        database: [{ validator: validateRequire }],
        local_database: [{ validator: validateRequire }],
        actor_id: [{ validator: validateRequire }]
      },
      tempRoute: {},

      path:"ws://yw_api.7cwan.com:9501",
      socket:"",

      dialogTableVisible: false,
      value: '',
      gridData: []
    }
  },

  mounted() {
    // 初始化
    this.init();
  },

  destroyed() {
    // 销毁监听
    this.socket.onclose = this.close
  },

  computed: {
    contentShortLength() {
      return this.postForm.content_short.length
    },
    displayTime: {
      // set and get is useful when the data
      // returned by the back end api is different from the front end
      // back end return => "2013-06-25 06:59:25"
      // front end need timestamp => 1372114765000
      get() {
        return (+new Date(this.postForm.display_time))
      },
      set(val) {
        this.postForm.display_time = new Date(val)
      }
    }
  },
  created() {
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id
      this.fetchData(id)
    }

    // Why need to make a copy of this.$route here?
    // Because if you enter this page and quickly switch tag, may be in the execution of the setTagsViewTitle function, this.$route is no longer pointing to the current page
    // https://github.com/PanJiaChen/vue-element-admin/issues/1221
    this.tempRoute = Object.assign({}, this.$route)
  },
  methods: {

    // v-el-drag-dialog onDrag callback function
    handleDrag() {
      this.$refs.select.blur()
    },

    init() {
      if(typeof(WebSocket) === "undefined"){
        alert("您的浏览器不支持socket")
      }else{
        // 实例化socket
        this.socket = new WebSocket(this.path)
        // 监听socket连接
        this.socket.onopen = this.open
        // 监听socket错误信息
        this.socket.onerror = this.error
        // 监听socket消息
        this.socket.onmessage = this.getMessage
      }
    },
    open: function () {
      console.log("socket连接成功")
    },
    error: function () {
      console.log("连接错误")
    },
    getMessage: function (msg) {

      this.gridData.push({data : msg.data})

      console.log(msg.data)
    },
    send: function () {
      this.socket.send(params)
    },
    close: function () {
      console.log("socket已经关闭")
    },

    fetchData(id) {
      fetchArticle(id).then(response => {
        this.postForm = response.data

        // just for test
        this.postForm.title += `   Article Id:${this.postForm.id}`
        this.postForm.content_short += `   Article Id:${this.postForm.id}`

        // set tagsview title
        this.setTagsViewTitle()

        // set page title
        this.setPageTitle()
      }).catch(err => {
        console.log(err)
      })
    },
    setTagsViewTitle() {
      const title = 'Edit Article'
      const route = Object.assign({}, this.tempRoute, { title: `${title}-${this.postForm.id}` })
      this.$store.dispatch('tagsView/updateVisitedView', route)
    },
    setPageTitle() {
      const title = 'Edit Article'
      document.title = `${title} - ${this.postForm.id}`
    },
    submitForm() {

      console.log(this.postForm)
      this.$refs.postForm.validate(valid => {
        if (valid) {
          this.dialogTableVisible = true;
          const context = this;
          this.loading = true
          pullData(this.postForm).then(res => {
            if (res.code != 200){
              this.$notify({
                title: 'fail',
                message: res.message,
                type: 'error',
                duration: 2000
              })
            } else{
              this.$notify({
                title: 'success',
                message: res.message,
                type: 'success',
                duration: 2000
              })
              this.queryData()
            }
            context.loading = false
            context.dialogFormVisible = false
          }).catch(function(error){
            context.loading = false
            context.dialogFormVisible = false
          });

        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    getRemoteUserList(query) {
      searchUser(query).then(response => {
        if (!response.data.items) return
        this.userListOptions = response.data.items.map(v => v.name)
      })
    }
  }
}
</script>

<style lang="scss" scoped>
@import "~@/styles/mixin.scss";

.createPost-container {
  position: relative;

  .createPost-main-container {
    padding: 40px 45px 20px 50px;

    .postInfo-container {
      position: relative;
      @include clearfix;
      margin-bottom: 10px;

      .postInfo-container-item {
        float: left;
      }
    }
  }

  .word-counter {
    width: 40px;
    position: absolute;
    right: 10px;
    top: 0px;
  }
}

.article-textarea /deep/ {
  textarea {
    padding-right: 40px;
    resize: none;
    border: none;
    border-radius: 0px;
    border-bottom: 1px solid #bfcbd9;
  }
}
</style>
