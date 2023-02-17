<style>
	@import "/static/mediate/css/common-user.css";
</style>
<template>
	<view>
		<view class="blank"></view>
		<view class="footer-common">
			<view class="footer-common-list">
				<view data-url="/pages/mediate/service-index" data-id='1' @click="jump">
					<view class="footer-common-img">
						<image class="footer-common-image" v-if="footIndex==1"
							src="/static/mediate/images/foot-a-open.png"></image>
						<image class="footer-common-image" v-else src="/static/mediate/images/foot-a-close.png"></image>
					</view>
					<text class="footer-common-text-already" v-if="footIndex==1">调解中心</text>
					<text class="footer-common-text" v-else>调解中心</text>
				</view>
			</view>
			<view class="footer-common-list">
				<view data-url="/pages/mediate/personal-index" data-id='4' @click="jump">
					<view class="footer-common-img">
						<image class="footer-common-image" v-if="footIndex==4"
							src="/static/mediate/images/foot-d-open.png"></image>
						<image class="footer-common-image" v-else src="/static/mediate/images/foot-d-close.png"></image>
					</view>
					<text class="footer-common-text-already" v-if="footIndex==4">个人中心</text>
					<text class="footer-common-text" v-else>个人中心</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				footIndex: 1,
			}
		},
		created() {
			this.getcache();
		},
		methods: {
			jump: function(e) {
				uni.setStorageSync('footIndex', e.currentTarget.dataset.id);
				uni.navigateTo({
					url: e.currentTarget.dataset.url,
				});
			},
			getcache: function(e) {
				this.footIndex = uni.getStorageSync('footIndex');
			}
		}
	}
</script>

<style>

</style>
