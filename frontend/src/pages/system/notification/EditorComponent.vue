<template>
  <div ref="vditorContainerRef" :class="VditorValue.length > 0 ? 'vditor' : 'vditorer'"></div>
  <!-- <el-button type="primary" @click="getContent">获取内容</el-button> -->
</template>

<script setup lang="ts">
// @ts-ignore
import Vditor from 'vditor';
import { onMounted, ref } from 'vue';
import 'vditor/dist/index.css';

const vditorContainerRef = ref(null);
let vditor: any;

// 富文本值
const VditorValue = ref('');
// 富文本绑定值
// const VditorValue = ref("ERP1.0版本1.问题公告上传图片![image](http://jxj.zhgdyun.com:15551/image/67ceba9e01fdb41366cc19e0.jpg)2.审批选择人搜索![image](http://jxj.zhgdyun.com:15551/image/67cebac601fdb41366cc19e1.jpg)备注：有问题及时沟通");

const init = () => {
  vditor = new Vditor(vditorContainerRef.value, {
    height: 550,
    mode: "wysiwyg",
    value: VditorValue.value,
    placeholder: "请输入内容...",
    toolbar: [
      "emoji",
      "headings",
      "bold",
      "italic",
      "strike",
      "link",
      "|",
      "list",
      "ordered-list",
      "check",
      "outdent",
      "indent",
      "|",
      "quote",
      "line",
      "code",
      "inline-code",
      "insert-before",
      "insert-after",
      "|",
      "upload",
      "record",
      "table",
      "|",
      "undo",
      "redo",
      "|",
      // 全屏按钮 - 顶部tap
      // "fullscreen",
      "edit-mode",
      {
        name: "more",
        toolbar: [
          "both",
          "code-theme",
          "content-theme",
          "export",
          "outline",
          "preview",
          "devtools",
          "info",
          "help",
        ],
      },
    ],
    toolbarConfig: {
      pin: false,
    },
    cache: {
      enable: false,
    },
    // 组件渲染完成之后-方法
    after: () => {
      // contentEditor.value.setValue("");
    },
    lang: 'zh_CN',
    // 其他 Vditor 配置选项
    upload: {
      url: 'http://jxj.zhgdyun.com:15551/upload/image',
      accept: 'image/*',
      withCredentials: true,
      fieldName: 'files',
      headers: {},
      success: (editor, msg) => {
        const data = JSON.parse(msg);
        if (data.status === 'SUCCESS') {
          const imageUrl = 'http://jxj.zhgdyun.com:15551/image/' + data.data[0].imageUrl;
          // 使用Markdown语法插入图片
          vditor.insertValue(`![image](${imageUrl})`);
        } else {
          console.error('上传失败1:', data.message);
        }
      },
      error: (msg) => {
        console.error('上传失败2:', msg);
      },
    },
  });
  console.log(vditor);

}

const getContent = () => {
  if (vditor) {
    const content = vditor.getValue();
    console.log('获取到的内容：', content);
    VditorValue.value = "";
  } else {
    console.error('Vditor 实例尚未初始化');
  }
}

onMounted(() => {
  init()
});

</script>

<style lang="less" scoped>
.vditor {
  width: 100% !important;

  ::v-deep .vditor-toolbar {
    display: none;
    width: 100% !important;
  }

  ::v-deep .vditor-content {
    pointer-events: none;

    img {
      pointer-events: all;
    }
  }
}

.vditorer {
  width: 100% !important;

  ::v-deep .vditor-toolbar {
    display: block !important;
    width: 100% !important;
  }

  ::v-deep .vditor-content {
    pointer-events: all;
  }
}
</style>