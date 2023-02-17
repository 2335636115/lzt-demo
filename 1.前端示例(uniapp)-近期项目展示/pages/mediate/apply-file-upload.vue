<template>
	<view>
		<uni-file-picker class="choosePicture" limit="5" file-mediatype="all" title="" v-model="imageValue" mode="grid" @select="select" @progress="progress" @success="success" @fail="fail"
			@delete="deletefile">
		</uni-file-picker>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				imageStyles: {
					width: 64,
					height: 64,
					border: {
						radius: '50%'
					}
				},
				listStyles: {
					border: true, // 是否显示边框					
					dividline: true, // 是否显示分隔线		
					borderStyle: { // 线条样式
						width: 1,
						color: 'blue',
						style: 'dashed',
						radius: 2
					}
				},
			}
		},
		methods: {
			// 获取上传状态
			select(e) {
				console.log('选择文件：', e)
				this.$http.upload('index/upload', e.tempFilePaths[0], {}, res => {
					console.log(res)
				})
			},
			// 获取上传进度
			progress(e) {
				console.log('上传进度：', e)
			},
			// 上传成功
			success(e) {
				console.log('上传成功')
			},
			// 上传失败
			fail(e) {
				console.log('上传失败：', e)
			},
			//文件从列表移除时触发
			deletefile(e) {
				console.log('上传失败：', e)
			}
		}
	}
</script>

<style lang="scss">
	.example-body {
		padding: 10px;
		padding-top: 0;
	}

	.custom-image-box {
		/* #ifndef APP-NVUE */
		display: flex;
		/* #endif */
		flex-direction: row;
		justify-content: space-between;
		align-items: center;
	}

	.text {
		font-size: 14px;
		color: #333;
	}
</style>
