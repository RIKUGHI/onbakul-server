const body = document.body
const baseUrl = 'http://localhost/onbakul-server/'

const loadDataPrint = async () => {
  const currentUrl = new URL(location)
  const inv = currentUrl.searchParams.get('inv')
  const id = currentUrl.searchParams.get('id_transaction')
  const name = currentUrl.searchParams.get('name')

  res = await fetch(baseUrl+'transactions/'+inv+'?id_transaction='+id, {
    method: 'GET'
  }).then(res => {
    if (res.ok) {
      return res.json()
    } else {
      console.log('Gagal Mengambil data');
    }
  })
  .then(data => data).catch(err => console.error('Gagal mengambil data'))

  acc = await fetch(baseUrl+'accounts/'+res.result.id_owner, {
    method: 'GET'
  }).then(res => {
    if (res.ok) {
      return res.json()
    } else {
      console.log('Gagal Mengambil data');
    }
  }).then(d => d).catch(e => console.log(e))

  // console.log(acc.result.business_name);
  if (res.response_code == 200) {
    body.innerHTML = componentTablePrint(res.result, name)
    setTimeout(() => {
      window.print()
    }, 400);
  } else {
    body.innerHTML = '<h2>Transaksi tidak ditemukan</h2>'
  }
}

loadDataPrint()

const componentTablePrint = (d, e) => {
  return  `
            <table border="0">
              <thead>
                <tr>
                  <th colspan="4">${e}</th>
                </tr>
                <tr>
                  <th colspan="4">====================================================================</th>
                </tr>
                <tr>
                  <td class="simple-info">
                    <p>Invoice</p>
                    <p>:</p>
                    <p>${d.invoice}</p>
                  </td>
                </tr>
                <tr>
                  <td class="simple-info">
                    <p>Tanggal</p>
                    <p>:</p>
                    <p>${dateToInaFormat(d.date)}</p>
                  </td>
                </tr>
                <tr>
                  <td class="simple-info">
                    <p>Metode</p>
                    <p>:</p>
                    <p>${methods[d.method]}</p>
                  </td>
                </tr>
                <tr>
                  <td colspan="4" align="center">---------------------------------------------------------------------------------------------------------------------</td>
                </tr>
                <tr>
                  <td align="center">Nama</td>
                  <td align="center">Harga</td>
                  <td align="center">Jumlah</td>
                  <td align="center">Sub Total</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="4" align="center">---------------------------------------------------------------------------------------------------------------------</td>
                </tr>
                ${d.details.map(d => {
                  return  `
                            <tr>
                              <td>${d.product_name}</td>
                              <td align="right">${formatRupiah(d.selling_price)}</td>
                              <td>${d.quantity}</td>
                              <td align="right">${formatRupiah(d.selling_price * d.quantity)}</td>
                            </tr>
                          `
                }).join('')}
                <tr>
                  <td colspan="4" align="center">---------------------------------------------------------------------------------------------------------------------</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3">Total</td>
                  <td>${formatRupiah(d.grand_total)}</td>
                </tr>
                <tr>
                  <td colspan="3">Bayar</td>
                  <td>${formatRupiah(d.paid_off)}</td>
                </tr>
                <tr>
                  <td colspan="3">kembali</td>
                  <td>${formatRupiah(d.paid_off - d.grand_total)}</td>
                </tr>
                <tr>
                  <td colspan="4" align="center">---------------------------------------------------------------------------------------------------------------------</td>
                </tr>
                <tr>
                  <td colspan="4">Terima Kasih Atas Kunjungan Anda</td>
                </tr>
              </tfoot>
            </table>
          `
}

const dateToInaFormat = date => {
  const split = date.split('-')
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des']
  return split[2]+' '+months[parseInt(split[1]) - 1]+' '+split[0]
}

const methods = ['Tunai', 'GoPay', 'Ovo', 'ShoppePay']

const formatRupiah = bilangan => {
  let number_string = bilangan.toString()
  let copyIntoArray = [...number_string]

  if (bilangan < 0) copyIntoArray.shift()

  let sisa 	= copyIntoArray.length % 3
  let rupiah 	= copyIntoArray.join('').substr(0, sisa)
  let ribuan 	= copyIntoArray.join('').substr(sisa).match(/\d{3}/g)

  if (ribuan) {
    let separator = sisa ? '.' : '';
    rupiah += separator + ribuan.join('.')
  }

  return 'Rp'+ (bilangan < 0 ? '-'+rupiah : rupiah)
}






















