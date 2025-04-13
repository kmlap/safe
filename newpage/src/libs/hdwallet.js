import bip39 from "bip39";
//import walletApi from "ethereumjs-wallet";
import hdkey from "ethereumjs-wallet/hdkey";
import bitcoin from 'bitcoinjs-lib'
import EthereumTx from 'ethereumjs-tx'
import tronWalletHd from 'tron-wallet-hd'

import { localSave, localRead } from "@/libs/util";
var defaultNetwork = bitcoin.networks.bitcoin

function getAddress(public1) {
        return bitcoin.address.toBase58Check(bitcoin.crypto.hash160(public1), defaultNetwork.pubKeyHash)
};

function PrefixZero(num, n) {
        return (Array(n).join(0) + num).slice(-n);
}

function generateAddress() {
        return new Promise((resolve, reject)=>{
                var mnemonic = bip39.generateMnemonic();
                var seed = bip39.mnemonicToSeed(mnemonic);
                var hdWallet = hdkey.fromMasterSeed(seed);
                var btckeypair = hdWallet.derivePath("m/44'/0'/0'/0/0");
                var ethkeypair = hdWallet.derivePath("m/44'/60'/0'/0/0");

                var ethAddress = '0x' + ethkeypair.getWallet().getAddress().toString('hex');
                var btcAddress = getAddress(btckeypair._hdkey._publicKey)

                // console.log(mnemonic)
                tronWalletHd.utils.generateAccountsWithMnemonic(mnemonic,1).then(
                    function (accounts) {
                        localSave('trxPrivateKey', accounts[0].privateKey)
                        localSave('trxAddress', accounts[0].address)
                        
                        resolve('done')
                    }
                );
                // var isValidSeed = tronWalletHd.utils.validateMnemonic(mnemonic);
                // console.log(accounts[0]) //false


                localSave('ethPrivateKey', ethkeypair.getWallet().getPrivateKey().toString('hex'))
                localSave('btcPrivateKey', btckeypair._hdkey._privateKey.toString('hex'))

                localSave('ethAddress', ethAddress)
                localSave('btcAddress', btcAddress)


                localSave('mnemonic', mnemonic)
        })
}

function recoveryKey(mnemonic) {
        return new Promise((resolve, reject)=>{
                var seed = bip39.mnemonicToSeed(mnemonic);
                var hdWallet = hdkey.fromMasterSeed(seed);
                var btckeypair = hdWallet.derivePath("m/44'/0'/0'/0/0");
                var ethkeypair = hdWallet.derivePath("m/44'/60'/0'/0/0");

                var ethAddress = '0x' + ethkeypair.getWallet().getAddress().toString('hex');
                var btcAddress = getAddress(btckeypair._hdkey._publicKey)

                tronWalletHd.utils.generateAccountsWithMnemonic(mnemonic,1).then(
                        function (accounts) {
                                localSave('trxPrivateKey', accounts[0].privateKey)
                                localSave('trxAddress', accounts[0].address)

                                resolve('done')
                        }
                );
                // var isValidSeed = tronWalletHd.utils.validateMnemonic(mnemonic);

                localSave('ethPrivateKey', ethkeypair.getWallet().getPrivateKey().toString('hex'))
                localSave('btcPrivateKey', btckeypair._hdkey._privateKey.toString('hex'))
                localSave('ethAddress', ethAddress)
                localSave('btcAddress', btcAddress)
                localSave('mnemonic', mnemonic)

        })
}

function signEthTransaction(nonce, to, value, gasLimit, gasPrice) {
        const txParams = {
                nonce: nonce,
                gasPrice: '0x' + parseInt((gasPrice * Math.pow(10, 18) / Number(gasLimit))).toString(16),
                gasLimit: gasLimit,
                to: to,
                value: value * Math.pow(10, 18),
                data: '',
                // EIP 155 chainId - mainnet: 1, ropsten: 3
                chainId: 1
        }
        const tx = new EthereumTx(txParams)
        var pri = localRead('ethPrivateKey')
        var ethPrivateKey = Buffer.from(pri, 'hex');
        tx.sign(ethPrivateKey)
        const serializedTx = '0x' + tx.serialize().toString('hex');
        return serializedTx
}

//wei:代币的位数/to  去除0x 的地址
function signTokenTransaction(nonce, contractAddress, to, value, gasLimit, gasPrice, wei) {
        console.log(value)
        value = PrefixZero((value * Math.pow(10, wei)).toString(16), 64)
        to = to.slice(2)
        to = PrefixZero(to, 64)
        const txParams = {
                nonce: nonce,
                gasPrice: '0x' + parseInt((gasPrice * Math.pow(10, 18) / Number(gasLimit))).toString(16),
                gasLimit: gasLimit,
                to: contractAddress,
                value: '0x00',
                data: '0x' + 'a9059cbb' + to + value,
                // EIP 155 chainId - mainnet: 1, ropsten: 3
                chainId: 1
        }
        const tx = new EthereumTx(txParams)
        var pri = localRead('ethPrivateKey')
        var ethPrivateKey = Buffer.from(pri, 'hex')
        tx.sign(ethPrivateKey)
        const serializedTx = '0x' + tx.serialize().toString('hex')
        return serializedTx
}

function signBtcTransaction(input, to, value, fee) {
        var txb = new bitcoin.TransactionBuilder(defaultNetwork)
        var pri = localRead('btcPrivateKey')
        var signer = new bitcoin.ECPair.fromPrivateKey(Buffer.from(pri, 'hex'), {
                compressed: true,
                network: defaultNetwork
        })

        txb.setVersion(1)
        var total = 0;
        var minNeed = parseFloat(value) + parseFloat(fee);
        var sub = 0;
        var num = 0;
        for (var i = 0; i < input.length; i++) {
                total += input[i].value_dec
                txb.addInput(input[i].txid, i)
                if (total >= minNeed) {
                        num = i + 1
                        sub = total - minNeed
                        break;
                }
        }
        txb.addOutput(to, parseInt(value * 100000000)) //转出地址
        txb.addOutput(localRead('btcAddress'), parseInt(sub * 100000000)) //找零地址  咱们设为自己
        for (let j = 0; j < num; j++) {
                txb.sign(j, signer)
        }
        // txb.sign(0, signer)
        return txb.build().toHex()
}



export {
        generateAddress,
        recoveryKey,
        signEthTransaction,
        signTokenTransaction,
        signBtcTransaction
}
