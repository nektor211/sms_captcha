clear;
format compact;
fid = fopen('data.txt', 'r');
data = textscan(fid, '%d%s', 'Delimiter','\t','HeaderLines',0);
img = reshape(rgb2gray(imread(data{2}{1})),1500,1);
val = classify(data{1}');
for i = 2:size(data{1})
  img = [img reshape(rgb2gray(imread(data{2}{i})),1500,1)];
end
c = size(data{1},1)
%c = 30
imgd = double(img(:,1:c));
val = val(:,1:c);
for k = 30:30
	hiddenLayerSize = k
	net = patternnet(hiddenLayerSize);
	net.trainParam.epochs=100;
	net.inputs{1}.processFcns = {'removeconstantrows','mapminmax'};
	net.outputs{2}.processFcns = {'removeconstantrows','mapminmax'};
	net.divideFcn = 'dividerand';  % Divide data randomly
	net.divideMode = 'sample';  % Divide up every sample
	net.trainParam.showWindow = false;
	net.divideParam.trainRatio = 70/100;
	net.divideParam.valRatio = 15/100;
	net.divideParam.testRatio = 15/100;
	net.trainFcn = 'trainlm';  % Levenberg-Marquardt
	net.trainFcn = 'trainscg';  % Levenberg-Marquardt
	net.performFcn = 'mse';  % Mean squared error
	[net,tr] = train(net,imgd,val);
	outputs = net(imgd);
	performance = perform(net,val,outputs)
	
end
%nprtool
