<style>
	@import "/static/mediate/css/login-index.css";
</style>
<template>
	<view>
		<image class="banner" src="/static/mediate/images/h-001.jpg"></image>
		<view class="title">调解服务管理平台</view>
		<form class="form-login" @submit="formSubmit">
			<view class="form-input">
				<view class="input-img">
					<image class="input-image" src="/static/mediate/images/h-002.png"></image>
				</view>
				<input type="text" name="username" class="input-content" placeholder="手机号/用户名"
					placeholder-style="color: #c7c7c7;" maxlength='24' v-model="username" />
			</view>
			<view class="form-input">
				<view class="input-img">
					<image class="input-image" src="/static/mediate/images/h-003.png"></image>
				</view>
				<input type="password" name="password" class="input-content" placeholder="密码"
					placeholder-style="color: #c7c7c7;" maxlength='24' v-model="password" />
			</view>
			<view class="form-remember">
				<checkbox color="#ed6611" :checked="rememberInfo" @click="changeRememberInfo" />
				<view class="remember-text">记住账户和密码</view>
			</view>
			<button form-type="submit" class="form-button">登录</button>
		</form>
	</view>
</template>

<script>
	import request from '@/common/js/mediator-request.js';
	export default {
		data() {
			return {
				rememberInfo: !1,
				username: '',
				password: ''
			}
		},
		created() {
			if (uni.getStorageSync('mediatorToken') != '') {
				uni.reLaunch({
					url: "/pages/mediator/case-list",
				});
			}
		},
		mounted() {
			//页面加载完成，获取本地存储的账号及密码  
			let userinfo = uni.getStorageSync('userinfo');
			if (userinfo) {
				userinfo = JSON.parse(userinfo);
				this.username = userinfo.username;
				this.password = userinfo.password;
				this.rememberInfo = userinfo.rememberinfo;
			}
		},
		methods: {
			changeRememberInfo: function(e) {
				this.rememberInfo = !this.rememberInfo;
			},
			formSubmit: function(e) {
				//获取表单数据
				var formdata = e.detail.value;
				formdata.rememberinfo = this.rememberInfo;
				//判断是否为空
				if ((formdata['username'] == '') || (formdata['password'] == '')) {
					uni.showToast({
						icon: 'error',
						title: '请填写账号/密码',
						duration: 2000
					});
				} else {
					request.post('mediator/login', formdata, result => {
						uni.setStorageSync('mediatorToken', result.data.token);
						// 记录密码
						if (formdata.rememberinfo) {
							uni.setStorageSync('userinfo', result.data.userinfo);
						} else {
							uni.removeStorageSync('userinfo');
						}
						uni.reLaunch({
							url: "/pages/mediator/case-list",
						});
					})
				}
			}
		}
	}
</script>

<style>

</style>
