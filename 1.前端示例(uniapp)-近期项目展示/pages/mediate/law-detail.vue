<style>
	@import "/static/mediate/css/law-detail.css";
</style>
<template>
	<view class="container">
		<!-- <form class="orange-back" @submit="formSubmit">
			<view class="search-content">
				<input type="text" name="search" class="search-input" placeholder="输入搜索内容"
					placeholder-style="color:white" :value="search" maxlength="30">
				<button form-type="submit" plain="true" class="search-button">
					<image class="search-button-glass" src="/static/mediate/images/g-001.png"></image>
				</button>
			</view>
		</form> -->
		<view class="detail-title">{{title}}</view>
		<view class="detail-content">
			<rich-text :nodes="content"></rich-text>
		</view>
	</view>
</template>

<script>
	import myFoot from "./footer.vue"

	export default {
		components: {
			myFoot,
		},
		data() {
			return {
				search: '',
				title: '',
				content: ''
			}
		},
		onShow() {
			this.getdetail();
		},
		methods: {
			getdetail() {
				var _this = this;
				let local = location.href;
				let id = this.getParam(local, "id");
				this.$http.get('mediate/article_detail', {
					id: id
				}, result => {
					_this.title = result.data.title
					_this.content = result.data.content
				})
			},
			getParam(path, name) {
				var reg = new RegExp("(^|\\?|&)" + name + "=([^&]*)(\\s|&|$)", "i");
				if (reg.test(path))
					return unescape(RegExp.$2.replace(/\+/g, " "));
				return "";
			},
			formSubmit: function(e) {
				var formdata = e.detail.value.search;
				if (formdata) {
					uni.navigateTo({
						url: '/pages/mediate/law-list?keyword=' + formdata,
					});
				}
			}
		}
	}
</script>

<style>

</style>
