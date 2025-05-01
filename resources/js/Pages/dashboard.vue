<template>
    <div class="dashboard">
        <div class="div-2">
            <div class="frame-16">
                <div class="frame-17">
                    <img class="group" alt="Group" :src="group92" />

                    <div class="group-2">
                        <div class="text-wrapper-14">Leaf Pos</div>

                        <div class="group-wrapper">
                            <div class="group-3" />
                        </div>
                    </div>
                </div>

                <div class="frame-18">
                    <SidebarposDashboard
                        class="sidebarpos-dashboard-instance"
                        frame="frame-9.svg"
                        property1="frame-1560"
                    />
                    <SidebarposProducts
                        iconFillChevronIconFillChevron="chevron-right-13.svg"
                        layerClassName="sidebarpos-products-instance"
                        property1="frame-1556"
                    />
                    <SidebarposInventory
                        frame="frame-10.svg"
                        iconFillChevronIconFillChevron="chevron-right-14.svg"
                        property1="frame-1560"
                    />
                    <SidebarposPurchases
                        iconFillChevronIconFillChevron="chevron-right-15.svg"
                        layerClassName="sidebarpos-purchases-instance"
                        property1="frame-1560"
                    />
                    <SidebarposPurchase
                        frame="frame-11.svg"
                        iconFillChevronIconFillChevron="chevron-right-16.svg"
                        property1="frame-1560"
                    />
                    <SidebarposSales
                        iconFillChevronIconFillChevron="chevron-right-17.svg"
                        layerClassName="sidebarpos-sales-instance"
                        property1="frame-1560"
                    />
                    <Sidebarpos
                        iconFillChevronIconFillChevron="chevron-right-18.svg"
                        layerClassName="sidebarpos-salereturns"
                        property1="frame-1560"
                    />
                    <SidebarposExpenses
                        iconFillChevronIconFillChevron="chevron-right-19.svg"
                        layer="layer-1-5.svg"
                        property1="frame-1560"
                    />
                    <SidebarposParties
                        iconFillChevronIconFillChevron="chevron-right-20.svg"
                        layerClassName="sidebarpos-parties-instance"
                        property1="frame-1560"
                    />
                    <PropertyFrameWrapper
                        iconFillChevronIconFillChevron="chevron-right-21.svg"
                        layer="layer-1-6.svg"
                        property1="frame-1560"
                    />
                    <SidebarposReports
                        iconFillChevronIconFillChevron="chevron-right-22.svg"
                        layerClassName="sidebarpos-reports-instance"
                        property1="frame-1560"
                    />
                    <SidebarposTaxes
                        frame="frame-12.svg"
                        iconFillChevronIconFillChevron="chevron-right-23.svg"
                        property1="frame-1560"
                    />
                    <SidebarposSettings
                        iconFillChevronIconFillChevron="chevron-right-24.svg"
                        layerClassName="sidebarpos-settings-instance"
                        property1="frame-1560"
                    />
                </div>
            </div>

            <div class="overlap-group">
                <p class="element-leaf-pos-all">
                    <span class="span">© 2025 </span>
                    <span class="text-wrapper-15">Leaf Pos.</span>
                    <span class="span"> All rights reserved.</span>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
// import Sidebarpos from "./Sidebarpos";
// import SidebarposDashboard from "./SidebarposDashboard";
// import SidebarposExpenses from "./SidebarposExpenses";
// import SidebarposInventory from "./SidebarposInventory";
// import SidebarposParties from "./SidebarposParties";
// import SidebarposProducts from "./SidebarposProducts";
// import SidebarposPurchase from "./SidebarposPurchase";
// import SidebarposPurchases from "./SidebarposPurchases";
// import SidebarposReports from "./SidebarposReports";
// import SidebarposSales from "./SidebarposSales";
// import SidebarposSettings from "./SidebarposSettings";
// import SidebarposTaxes from "./SidebarposTaxes";
// import PropertyFrameWrapper from "./PropertyFrameWrapper";
// import group92 from "./group-9-2.png";

export default {
    name: "Dashboard",
    components: {
        Sidebarpos,
        SidebarposDashboard,
        SidebarposExpenses,
        SidebarposInventory,
        SidebarposParties,
        SidebarposProducts,
        SidebarposPurchase,
        SidebarposPurchases,
        SidebarposReports,
        SidebarposSales,
        SidebarposSettings,
        SidebarposTaxes,
        PropertyFrameWrapper,
    },
    data() {
        return {
            group92,
        };
    },
};
</script>

<style>
.dashboard {
    background-color: #eeeeee;
    display: flex;
    flex-direction: row;
    justify-content: center;
    width: 100%;
}

.dashboard .div-2 {
    background-color: #eeeeee;
    height: 1024px;
    position: relative;
    width: 1440px;
}

.dashboard .frame-16 {
    align-items: center;
    background-color: #2f8451;
    display: flex;
    flex-direction: column;
    gap: 1px;
    height: 1024px;
    left: 0;
    position: absolute;
    top: 0;
    width: 280px;
}

.dashboard .frame-17 {
    align-items: center;
    background-color: #2f8451;
    display: inline-flex;
    flex: 0 0 auto;
    gap: 10px;
    justify-content: center;
    padding: 50px 0px;
    position: relative;
}

.dashboard .group {
    height: 41.02px;
    position: relative;
    width: 29px;
}

.dashboard .group-2 {
    height: 38px;
    margin-right: -2.00px;
    position: relative;
    width: 116.82px;
}

.dashboard .text-wrapper-14 {
    color: #ffffff;
    font-family: "Poppins-SemiBold", Helvetica;
    font-size: 25px;
    font-weight: 600;
    left: 0;
    letter-spacing: 0;
    line-height: normal;
    position: absolute;
    top: 0;
}

.dashboard .group-wrapper {
    background-color: #ffffff;
    border-radius: 5.41px;
    height: 11px;
    left: 104px;
    position: absolute;
    top: 8px;
    width: 11px;
}

.dashboard .group-3 {
    background-color: #2f8451;
    background-image: url(./line-1.svg);
    background-size: 100% 100%;
    height: 5px;
    left: 3px;
    position: relative;
    top: 3px;
    width: 4px;
}

.dashboard .frame-18 {
    align-items: flex-start;
    display: inline-flex;
    flex: 0 0 auto;
    flex-direction: column;
    gap: 12px;
    position: relative;
}

.dashboard .sidebarpos-dashboard-instance {
    background-color: #ffffff33 !important;
    border-radius: 10px !important;
}

//.dashboard .sidebarpos-products-instance {
//    background-image: url(./group-14.png) !important;
//}
//
//.dashboard .sidebarpos-purchases-instance {
//    background-image: url(./group-15.png) !important;
//}
//
//.dashboard .sidebarpos-sales-instance {
//    background-image: url(./group-16.png) !important;
//}
//
//.dashboard .sidebarpos-salereturns {
//    background-image: url(./group-17.png) !important;
//}
//
//.dashboard .sidebarpos-parties-instance {
//    background-image: url(./group-18.png) !important;
//}
//
//.dashboard .sidebarpos-reports-instance {
//    background-image: url(./group-19.png) !important;
//}
//
//.dashboard .sidebarpos-settings-instance {
//    background-image: url(./group-20.png) !important;
//}

.dashboard .overlap-group {
    background-color: #ffffff;
    height: 55px;
    left: 280px;
    position: absolute;
    top: 969px;
    width: 1160px;
}

.dashboard .element-leaf-pos-all {
    color: transparent;
    font-family: "Poppins-Regular", Helvetica;
    font-size: 12px;
    font-weight: 400;
    left: 16px;
    letter-spacing: 0;
    line-height: 24px;
    position: absolute;
    top: 15px;
    white-space: nowrap;
}

.dashboard .span {
    color: #000000;
}

.dashboard .text-wrapper-15 {
    color: #2f8451;
}
</style>
