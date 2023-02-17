<style>
	@import "/static/mediate/css/mine-conclusion.css";
</style>
<template>
	<view>
		<form @submit="sumbit">
			<view class="orange-back-information">
				<view class="information-describe">回访总结</view>
				<view class="describe-textarea">
					<textarea name="content" class="describe-textarea-content" auto-height="true" maxlength="-1" />
				</view>
			</view>
			<button form-type="submit" class="form-button">确定</button>
		</form>
		<view class="status-success" :class="specClass">
			<view class="success-tip">
				<view class="success-tip-img">
					<image class="success-tip-image" src="/static/mediate/images/c-012.png" mode=""></image>
				</view>
				<view class="success-tip-title">提交成功</view>
				<view class="success-tip-content">已完成回访，资料已提交成功！</view>
				<button class="success-tip-button" @click="close_tip">确定</button>
			</view>
		</view>
	</view>
	<!-- <myFoot></myFoot> -->
</template>

<script>
	import request from '../../common/js/mediator-request.js';
	import myHead from "./header.vue"
	import myFoot from "./footer.vue"

	export default {
		components: {
			myHead,
			myFoot
		},
		data() {
			return {
				specClass: 'hide',
				id: '',
				workorder_id: '',
			}
		},
		onLoad(values) {
			this.id = values.id;
			this.workorder_id = values.workorder_id;
		},
		methods: {
			close_tip: function(e) {
				uni.redirectTo({
					url: '/pages/mediator/mine-record?id=' + this.id,
				});
			},
			sumbit: function(e) {
				//获取表单数据
				var formdata = e.detail.value;
				//判断是否为空
				if (formdata['content '] == '') {
					uni.showToast({
						icon: 'error',
						title: '内容不能为空',
						duration: 2000
					});
				} else {
					request.post('mediator/visit', {
						workorder_id: this.workorder_id,
						content: formdata['content'],
					}, result => {
						if (result.code == 1) {
							this.specClass = "show";
						}
					})
				}
			}
		}
	}
</script>

<style>

</style>
