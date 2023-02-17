// index.js
var App = getApp();
Page({
  data: {
    userInfo: {},
    loginInfo: {
      code: 0,
    },
  },
  onLoad() {

  },
  //获取用户信息
  getUserProfile(e) {
    wx.getUserProfile({
      desc: '展示用户信息', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
      success: (res) => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    })
  },
  //登录按钮触发的事件
  login() {
    let _this = this;
    //获取openId，需要code来换取，先用“wx.login”来获取code
    wx.login({
      success: (res) => {
        let code = res.code // 获取code值
        wx.request({
          data: {
            code: code
          },
          url: 'https://www.fengjian123.com/Index/wxlogin',  //然后调用接口，将code传后端
          success: (res) => {
            console.log(res.data);
          }
        })
      }
    })
    _this.getUserProfile();  //获取用户信息
  }
})