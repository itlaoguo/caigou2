<template>
  <div class="dash-box" style="min-height: calc(100vh - 130px);">
    <t-row>
      <t-col :span="12">
        <t-row :gutter="[15, 15]">
          <t-col :xxl="3" :xl="3" :xs="6">
            <div class="home-card" :bordered="false">

              <!-- 左右布局 -->
              <div class="homec-left">
                <div class="name">今日采购订单</div>
                <div class="position">0</div>
                <div class="compare">
                </div>
              </div>

              <!-- 左右布局 -->
              <div class="homec-right">
                <t-avatar size="70px">
                  <t-icon name="catalog" />
                </t-avatar>
              </div>

            </div>
          </t-col>
          <t-col :xxl="3" :xl="3" :xs="6">
            <div class="home-card" :bordered="false">
              <!-- 左右布局 -->
              <div class="homec-left">
                <div class="name">总采购订单数</div>
                <div class="position">{{ 0 }}</div>
                <div class="compare">
                </div>
              </div>

              <!-- 左右布局 -->
              <div class="homec-right">
                <t-avatar size="70px">
                  <t-icon name="catalog" />
                </t-avatar>
              </div>
            </div>
          </t-col>
          <t-col :xxl="3" :xl="3" :xs="6">
            <div class="home-card" :bordered="false">

              <!-- 左右布局 -->
              <div class="homec-left">
                <div class="name">今日采购商品</div>
                <div class="position">0</div>
                <div class="compare">
                </div>
              </div>

              <!-- 左右布局 -->
              <div class="homec-right">
                <t-avatar size="70px">
                  <t-icon name="control-platform" />
                </t-avatar>
              </div>

            </div>
          </t-col>
          <t-col :xxl="3" :xl="3" :xs="6">
            <div class="home-card" :bordered="false">

              <!-- 左右布局 -->
              <div class="homec-left">
                <div class="name">总采购商品数</div>
                <div class="position">0</div>
                <div class="compare">
                </div>
              </div>

              <!-- 左右布局 -->
              <div class="homec-right">
                <t-avatar size="70px">
                  <t-icon name="control-platform" />
                </t-avatar>
              </div>
            </div>
          </t-col>
        </t-row>
      </t-col>
    </t-row>
  </div>
</template>

<script lang="ts">
export default {
  name: 'DashboardBase',
}
</script>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

onMounted(() => {


});

</script>

<style lang="less" scoped>
.home-card {
  width: 100%;
  background: #fff;
  height: 160px;
  border-radius: 12px;
  padding: 24px;
  // box-shadow: 0 4px 12px rgba(49, 195, 166, 0.3);
  cursor: pointer;
  margin-bottom: 1%;
  transition: all 0.3s;
  display: flex;
  align-items: center;


  &:hover {
    transform: scale(1.03);
    margin-bottom: 2%;
  }

  // 新增布局控制
  display: flex;
  justify-content: space-between;

  // 响应式适配
  @media (max-width: 768px) {
    width: 48%;
    height: 140px;
  }
}


// ================================新版样式==========================


.homec-left {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  flex: 1;

  .name {
    font-size: 15px;
    color: #909399;
    letter-spacing: 0.5px;
  }

  .position {
    font-size: 32px;
    font-weight: 600;
    color: #303133;
    margin: 13px 0 15px 0;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);

    &:hover {
      transform: scale(1.05);
      color: #409eff;
    }
  }

  .compare {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    // background: rgba(103, 194, 58, 0.1);
    border-radius: 12px;
    transition: transform 0.2s;

    .icon {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    // 增长
    .arrow-up {
      color: #2ba471;
      background: #c6f3d7;
    }

    // 减少
    .arrow-down {
      color: #d54941;
      background: #ffd8d2;
    }

    // 持平
    .arrow-right {
      color: #4d4c4c;
      background: #bebdbd;
    }

    &:hover {
      transform: translateY(-2px);
    }
  }
}

.compare:hover>div::before {
  transform: translateY(-2px);
}



.dash-box {
  width: 100%;
  padding: 20px;

  // 公共样式
  .con-box {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    display: flex;
    flex-direction: column;

    // 标题
    .conbox-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 15px;

      .conbox-left,
      .conbox-right {
        display: flex;
        align-items: center;
      }

      .conbox-left {
        font-size: 18px;
        font-weight: 700;
      }

      .conbox-right {
        color: #8993a3;
      }
    }
  }

  .one-row {
    .conbox-con {
      display: flex;
      flex-wrap: wrap;
      align-content: flex-start;
      gap: 1%;
      // justify-content: space-between;

      .home-card {
        width: 15.6%;
        // background: linear-gradient(135deg, #31c3a6 20%, #2aab92 100%);
        height: 160px;
        border-radius: 12px;
        padding: 24px;
        // box-shadow: 0 4px 12px rgba(49, 195, 166, 0.3);
        cursor: pointer;
        margin-bottom: 1%;
        transition: all 0.3s;


        &:hover {
          transform: scale(1.03);
          margin-bottom: 2%;
        }

        // 新增布局控制
        display: flex;
        justify-content: space-between;

        // 响应式适配
        @media (max-width: 768px) {
          width: 48%;
          height: 140px;
        }
      }

      .hcard-tit {
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        .name {
          font-size: 20px;
          // color: rgba(255, 255, 255, 0.95);
          letter-spacing: 1px;
        }
      }

      .position {
        font-size: 25px;
        font-weight: 700;
        // color: rgba(255, 255, 255, 0.9);
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        align-self: flex-end;
        transition: all 0.3s;
        // margin-bottom: 10px;

        // 动态效果（鼠标悬停时）
        &:hover {
          transform: scale(1.12);
          opacity: 0.9;
        }
      }

    }
  }

  .two-row,
  .there-row {
    .conbox-con {
      height: 450px;
    }
  }
}
</style>
