<style>
	@import "/static/mediate/css/common-mediation.css";
</style>
<template>
	<view>
		<view class="blank"></view>
		<view class="footer-common">
			<view class="footer-common-list">
				<view data-url="/pages/mediator/case-list" data-id='1' @click="jump">
					<view class="footer-common-img">
						<image class="footer-common-image" v-if="footIndex==1"
							src="/static/mediate/images/foot-g-open.png"></image>
						<image class="footer-common-image" v-else src="/static/mediate/images/foot-g-close.png"></image>
					</view>
					<text class="footer-common-text">事项</text>
				</view>
			</view>
			<view class="footer-common-list">
				<view data-url="/pages/mediator/report-index" data-id='2' @click="jump">
					<view class="footer-common-img">
						<image class="footer-common-image" v-if="footIndex==2"
							src="/static/mediate/images/foot-e-open.png"></image>
						<image class="footer-common-image" v-else src="/static/mediate/images/foot-e-close.png"></image>
					</view>
					<text class="footer-common-text">上报</text>
				</view>
			</view>
			<view class="footer-common-list">
				<view data-url="/pages/mediator/mine-index" data-id='3' @click="jump">
					<view class="footer-common-img">
						<image class="footer-common-image" v-if="footIndex==3"
							src="/static/mediate/images/foot-f-open.png"></image>
						<image class="footer-common-image" v-else src="/static/mediate/images/foot-f-close.png"></image>
					</view>
					<text class="footer-common-text">我的</text>
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
				uni.setStorageSync('mediatorFootIndex', e.currentTarget.dataset.id);
				uni.navigateTo({
					url: e.currentTarget.dataset.url,
				});
			},
			getcache: function(e) {
				this.footIndex = uni.getStorageSync('mediatorFootIndex');
			}
		}
	}
</script>

<style>

</style>
