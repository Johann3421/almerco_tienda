import { defineStore } from 'pinia';

export const useSettingStore = defineStore("SettingStore", {
  state: () => ({
    dolarValue: 0.00,
    imagsupvalue: "",
    imagmedvalue: "",
    imagsupmobilevalue: "",
    imagmedmobilevalue: "",
  }),
  actions: {
    updateDolarValue(dolar: number, urlsup: string, urlmedio: string, urlsupmobile: string, urlmediomobile: string) {
      this.dolarValue = dolar;
      this.imagsupvalue = urlsup;
      this.imagmedvalue = urlmedio;
      this.imagsupmobilevalue = urlsupmobile;
      this.imagmedmobilevalue = urlmediomobile;
    }
  },
  getters: {
    getDolarValue: (state) => state.dolarValue,
    getImagsupvalue: (state) => state.imagsupvalue,
    getImagmedvalue: (state) => state.imagmedvalue,
    getImagsupmobilevalue: (state) => state.imagsupmobilevalue,
    getImagmedmobilevalue: (state) => state.imagmedmobilevalue,
  },
});
